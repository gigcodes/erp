<?php

namespace App\Http\Controllers;

use App\BankStatement;
use App\BankStatementFile;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;

class BankStatementController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data = BankStatementFile::with('user')->paginate(25);
        
        return View('bank-statement.index',
            compact('data')
        );
    }

    public function showImportForm()
    {
        return view('bank-statement.form');
    }

    public function import(Request $request)
    {
        $file = $request->file('excel_file');
        
        // Extract file information from the temporary path
        $tempPath = $file->path();
        $originalName = $file->getClientOriginalName();
        $mimeType = mime_content_type($tempPath);
        $size = filesize($tempPath);
        $extension = $file->getClientOriginalExtension();
        
        // $fileName = time() . '_' . $file->getClientOriginalName();
        $fileName = md5(time()) . '.' . $extension;
        $path = $file->move(storage_path('app/files/bank_statements'), $fileName);
        $path = 'files/bank_statements/'.$fileName;
      
        // Create an UploadedFile instance manually
        // $uploadedFile = new UploadedFile(
        //     $tempPath,       // Temporary path
        //     $originalName,   // Original file name
        //     $mimeType,       // File mime type
        //     $size           // File size
        // );
        // $path = $uploadedFile->store('files/bank_statements');

        $bankStatement = BankStatementFile::create([
            'filename' => $originalName,
            'path' => $path,
            'mapping_fields' => '',
            'status' => 'uploaded',
            'created_by' => \Auth::id(),
            'created_at' => date("Y-m-d H:i:s")
        ]);
        
        return redirect()->back()->with('success', 'File imported successfully.');
    }
    
    public function heading_row_number_check(Request $request)
    {
        $input = $request->all();
        return redirect()->route('bank-statement.import.map', ['id' => $input['id'], 'heading_row_number' => $input['heading_row_number']]);       
    }

    public function map(Request $request, $id, $heading_row_number = 1)
    {
        $bankStatement = BankStatementFile::find($id);
        $filePath = storage_path("app/".$bankStatement->path); //read file path
        // $filePath = $bankStatement->path; //read file path
        
        $data = Excel::toArray([], $filePath);
        
        // Assuming the first row contains column headers
        $excelHeaders = $data[0][$heading_row_number-1];

        // Get the columns of the database table
        // $dbFields = \Schema::getColumnListing('bank_statement'); // Replace with your actual table name
        $dbFields = [
            'transaction_date' => 'Transaction Date',
            'transaction_reference_no' => 'Transaction Reference Number',
            'debit_amount' => "Debit Amount",
            'credit_amount' => "Credit Amount",
            'balance' => "Balance"
        ];

        $row_count = count($data[0]);
        return view('bank-statement.map', compact('bankStatement','excelHeaders', 'dbFields', 'id', 'row_count', 'heading_row_number'));
    }

    public function map_import(Request $request, $id, $heading_row_number = 1)
    {
        $bankStatementFile = BankStatementFile::find($id);
        $filePath = storage_path("app/".$bankStatementFile->path); //read file path
        // $filePath = $bankStatementFile->path; //read file path
       
        $data = Excel::toArray([], $filePath);
        $number = $heading_row_number-1;
        if($number <= 0){
            $number = 0;
        }
        // Assuming the first row contains column headers
        $excelHeaders = $data[0][$number];
        
        $data_array = [];
        
        foreach($data[0] as $k=>$v){
            foreach($excelHeaders as $k1=>$v1){
                $data_array[$k][trim($v1)] = $v[trim($k1)];
            }
        }
        
        $fields_db = [
            "transaction_date",
            "transaction_reference_no",
            "debit_amount",
            "credit_amount",
            "balance"
        ];

        $data_array_new = [];
        $inputes = $request->all();
        foreach($data_array as $k=>$v){
            $data_array_new_1 = [];
            foreach($fields_db  as $k1=>$v1){
                $data_array_new_1[trim($v1)] = @$v[trim($inputes[$v1])]; 
            }
            $data_array_new_1['bank_statement_file_id'] = $id;
            $data_array_new_1['created_at'] = date("Y-m-d H:i:s");
            // $data_array_new[] = $data_array_new_1;
            foreach($data_array_new_1 as $k2=>$v2){
                if($v2 == null || trim($v2) == ""){
                    $data_array_new_1[$k2] = "-";
                }
            }
           
            $bankStatement = BankStatement::create($data_array_new_1);
        }

        //save status of the file
        $bankStatementFile->status = 'mapped';
        $bankStatementFile->save();

        return redirect()->route('bank-statement.index')->with('success', 'File imported data mapped successfully.');
    }
    
    public function mapped_data($id, Request $request)
    {
        $data = BankStatement::where(['bank_statement_file_id' => $id])->with('user')->paginate(25);
        $bankStatementFile = BankStatementFile::find($id);
        return View('bank-statement.mapped',
            compact('data', 'bankStatementFile')
        );
    }
    
}