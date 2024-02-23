<?php

namespace App\Http\Controllers;

use App\Sop;
use App\Task;
use App\User;
use App\Vendor;
use App\DeveloperTask;
use App\UserFeedbackStatus;
use Illuminate\Http\Request;
use App\UserFeedbackCategory;
use App\Models\DataTableColumn;
use App\UserFeedbackStatusUpdate;
use App\Models\UserFeedbackRemark;
use App\UserFeedbackCategorySopHistory;
use App\UserFeedbackCategorySopHistoryComment;

class UserManagementController extends Controller
{
    public function addFeedbackTableData(Request $request)
    {
        $status = UserFeedbackStatus::get();
        $user_id = '';
        if (\Auth::user()->isAdmin() == true) {
            $category = UserFeedbackCategory::select('id', 'user_id', 'sop_id', 'category', 'sop')->groupBy('category');
        } else {
            $category = UserFeedbackCategory::select('id', 'user_id', 'sop_id', 'category', 'sop')->groupBy('category');
            $category = $category->get();
            if (empty($category[0]->id)) {
                $category = UserFeedbackCategory::select('id', 'user_id', 'sop_id', 'category', 'sop')->groupBy('category');
            }
        }

        if ($request->user_id) {
            if (\Auth::user()->isAdmin() == true) {
                $user_id = $request->user_id;
            } else {
                $user_id = \Auth::user()->id;
            }
        }
        $sops = Sop::all();
        $users = User::all();
        $sops = Sop::all();
        $category = $category->get();

        $query = Vendor::where('feeback_status', 1);

        if (request('term') != null) {
            $query = $query->where('name', 'LIKE', "%{$request->term}%");
        }

        $vendors = $query->paginate(25);

        $datatableModel = DataTableColumn::select('column_name')->where('user_id', auth()->user()->id)->where('section_name', 'get-feedback-table-data')->first();

        $dynamicColumnsToShowVendorsFeeback = [];
        if (! empty($datatableModel->column_name)) {
            $hideColumns = $datatableModel->column_name ?? '';
            $dynamicColumnsToShowVendorsFeeback = json_decode($hideColumns, true);
        }

        return view('user-management.get-user-feedback-table', compact('category', 'status', 'user_id', 'users', 'request', 'sops', 'vendors', 'dynamicColumnsToShowVendorsFeeback'));
    }

    public function vendorFeedbackVolumnVisbilityUpdate(Request $request)
    {
        $userCheck = DataTableColumn::where('user_id', auth()->user()->id)->where('section_name', 'get-feedback-table-data')->first();

        if ($userCheck) {
            $column = DataTableColumn::find($userCheck->id);
            $column->section_name = 'get-feedback-table-data';
            $column->column_name = json_encode($request->column_vendorsf);
            $column->save();
        } else {
            $column = new DataTableColumn();
            $column->section_name = 'get-feedback-table-data';
            $column->column_name = json_encode($request->column_vendorsf);
            $column->user_id = auth()->user()->id;
            $column->save();
        }

        return redirect()->back()->with('success', 'column visiblity Added Successfully!');
    }

    public function taskCount(Request $request, $user_feedback_cat_id, $user_feedback_user_id, $user_feedback_vendor_id)
    {
        $taskStatistics['Devtask'] = DeveloperTask::where('user_feedback_cat_id', $user_feedback_cat_id)->where('user_feedback_vendor_id', $user_feedback_vendor_id)->where('status', '!=', 'Done')->select();

        $query = DeveloperTask::join('users', 'users.id', 'developer_tasks.assigned_to')->where('user_feedback_cat_id', $user_feedback_cat_id)->where('status', '!=', 'Done');
        if ($request->user_id != '' && (\Auth::user()->isAdmin)) {
            $query->where('users.id', $request->user_id);
        } elseif ((\Auth::user()->isAdmin) == false) {
            $query->where('users.id', $request->user_id);
        }

        $query->select('developer_tasks.id', 'developer_tasks.task as subject', 'developer_tasks.status', 'users.name as assigned_to_name');
        $query = $query->addSelect(\DB::raw("'Devtask' as task_type,'developer_task' as message_type"));
        $taskStatistics = $query->get();
        $othertask = Task::where('user_feedback_cat_id', $user_feedback_cat_id)->where('user_feedback_vendor_id', $user_feedback_vendor_id)->whereNull('is_completed')->select();
        $query1 = Task::join('users', 'users.id', 'tasks.assign_to')->where('user_feedback_vendor_id', $user_feedback_vendor_id)->where('user_feedback_cat_id', $user_feedback_cat_id)->whereNull('is_completed');
        $query1->select('tasks.id', 'tasks.task_subject as subject', 'tasks.assign_status', 'users.name as assigned_to_name');
        $query1 = $query1->addSelect(\DB::raw("'Othertask' as task_type,'task' as message_type"));
        $othertaskStatistics = $query1->get();
        $merged = $othertaskStatistics->merge($taskStatistics);

        return response()->json(['code' => 200, 'taskStatistics' => $merged]);
    }

    public function sopHistory(Request $request)
    {
        try {
            if ($request->sop_text == '') {
                return response()->json(['code' => '500', 'message' => 'Please enter sop name']);
            }
            $sop = new UserFeedbackCategorySopHistory();
            $sop->category_id = $request->cat_id;
            $sop->user_id = \Auth::user()->id;
            $sop->sop = $request->sop_text;
            $sop->sops_id = $request->sops_id;
            $sop->vendor_id = $request->vendor_id;
            $sop->save();
            UserFeedbackCategory::where('id', $request->cat_id)->update(['sop_id' => $sop->id, 'sop' => $request->sop_text, 'sops_id' => $request->sops_id]);

            return response()->json(['code' => '200', 'data' => $sop, 'message' => 'Data saved successfully']);
        } catch (\Exception $e) {
            return response()->json(['code' => '500', 'message' => $e->getMessage()]);
        }
    }

    public function getSopHistory(Request $request)
    {
        try {
            if ($request->cat_id == '') {
                return response()->json(['code' => '500', 'message' => 'History not found']);
            }
            $sop = UserFeedbackCategorySopHistory::where('category_id', $request->cat_id)->where('vendor_id', $request->vendor_id)->orderBy('id', 'DESC')->get();
            $html = '';
            if ($sop) {
                foreach ($sop as $value) {
                    $sop_data = Sop::where('id', $value->sops_id)->first();
                    $html .= '<tr><td>' . $value->id . '</td><td>' . $value->sop . '</td>';
                    if (! empty($sop_data)) {
                        $html .= "<td class='expand-row-msg' data-name='content' data-id='$value->id'>
                                <div class='sop-short-content-$value->id'>" . \Str::of($sop_data->content)->limit(25, '...') . "</div>
                                <div style='word-break:break-all;' class='sop-full-content-$value->id hidden'>" . $sop_data->content . '</div></td>';
                    } else {
                        $html .= '<td></td>';
                    }
                    $html .= '</tr>';
                }
            } else {
                $html .= '<tr><td colspan="4" class="text-center">No data found</td></tr>';
            }

            return response()->json(['code' => '200', 'data' => $html, 'message' => 'Data listed successfully']);
        } catch (\Exception $e) {
            return response()->json(['code' => '500', 'message' => $e->getMessage()]);
        }
    }

    public function getSopCommentHistory(Request $request)
    {
        try {
            if ($request->sop_history_id == '') {
                return response()->json(['code' => '200', 'data' => [], 'message' => 'Data listed successfully']);
            }
            $sopComment = UserFeedbackCategorySopHistoryComment::leftJoin('users', 'users.id', 'user_feedback_category_sop_history_comments.user_id')
                ->select('user_feedback_category_sop_history_comments.*', 'users.name As username')
                ->where('sop_history_id', $request->sop_history_id)->get();

            return response()->json(['code' => '200', 'data' => $sopComment, 'message' => 'Data listed successfully']);
        } catch (\Exception $e) {
            return response()->json(['code' => '500', 'message' => $e->getMessage()]);
        }
    }

    public function sopHistoryComment(Request $request)
    {
        try {
            $sopComment = new UserFeedbackCategorySopHistoryComment();
            $sopComment->sop_history_id = $request->sop_history_id;
            $sopComment->user_id = \Auth::user()->id;
            $sopComment->comment = $request->comment;
            $sopComment->accept_reject = $request->accept_reject;
            $sopComment->save();

            return response()->json(['code' => '200', 'data' => $sopComment, 'message' => 'Comment saved successfully']);
        } catch (\Exception $e) {
            return response()->json(['code' => '500', 'message' => $e->getMessage()]);
        }
    }

    public function statusHistory(Request $request)
    {
        try {
            $sop = new UserFeedbackStatusUpdate();
            $sop->user_feedback_category_id = $request->cat_id;
            $sop->user_id = \Auth::user()->id;
            $sop->user_feedback_status_id = $request->status_id;
            $sop->user_feedback_vendor_id = $request->vendor_id;
            $sop->save();

            return response()->json(['code' => '200', 'data' => $sop, 'message' => 'Data saved successfully']);
        } catch (\Exception $e) {
            return response()->json(['code' => '500', 'message' => $e->getMessage()]);
        }
    }

    public function getStatusHistory(Request $request)
    {
        try {
            if ($request->cat_id == '') {
                return response()->json(['code' => '500', 'message' => 'History not found']);
            }
            $sop = UserFeedbackStatusUpdate::where('user_feedback_category_id', $request->cat_id)->where('user_feedback_vendor_id', $request->vendor_id)->orderBy('id', 'DESC')->get();
            $html = '';
            if ($sop) {
                foreach ($sop as $value) {
                    $status_data = UserFeedbackStatus::where('id', $value->user_feedback_status_id)->first();
                    $html .= '<tr><td>' . $value->id . '</td><td>' . $status_data->status . '</td>';
                    $html .= '</tr>';
                }
            } else {
                $html .= '<tr><td colspan="4" class="text-center">No data found</td></tr>';
            }

            return response()->json(['code' => '200', 'data' => $html, 'message' => 'Data listed successfully']);
        } catch (\Exception $e) {
            return response()->json(['code' => '500', 'message' => $e->getMessage()]);
        }
    }

    public function statusCreate(Request $request)
    {
        try {
            $status = new UserFeedbackStatus();
            $status->status = $request->status_name;
            $status->save();

            return response()->json(['code' => 200, 'message' => 'status Create successfully']);
        } catch (\Exception $e) {
            $msg = $e->getMessage();

            return response()->json(['code' => 500, 'message' => $msg]);
        }
    }

    public function statuscolor(Request $request)
    {
        $status_color = $request->all();
        $data = $request->except('_token');
        foreach ($status_color['color_name'] as $key => $value) {
            $bugstatus = UserFeedbackStatus::find($key);
            $bugstatus->status_color = $value;
            $bugstatus->save();
        }

        return redirect()->back()->with('success', 'The status color updated successfully.');
    }

    public function saveRemarks(Request $request)
    {
        $post = $request->all();

        $this->validate($request, [
            'user_feedback_category_id' => 'required',
            'user_feedback_vendor_id' => 'required',
            'remarks' => 'required',
        ]);

        $input = $request->except(['_token']);
        $input['added_by'] = \Auth::user()->id;
        UserFeedbackRemark::create($input);

        return response()->json(['code' => 200, 'data' => $input]);
    }

    public function getRemarksHistories(Request $request)
    {
        $datas = UserFeedbackRemark::with(['user'])
            ->where('user_feedback_category_id', $request->user_feedback_category_id)
            ->where('user_feedback_vendor_id', $request->user_feedback_vendor_id)
            ->latest()
            ->get();

        return response()->json([
            'status' => true,
            'data' => $datas,
            'message' => 'History get successfully',
            'status_name' => 'success',
        ], 200);
    }
}
