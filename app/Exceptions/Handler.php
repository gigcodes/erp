<?php

namespace App\Exceptions;

use App\Email;
use Exception;
use Throwable;
use App\EmailLog;
use Illuminate\Http\Request;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Report or log an exception.
     *
     * @return void
     */
    public function report(Throwable $exception)
    {
        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  Request  $request
     */
    public function render($request, Throwable $exception)
    {
        if ($exception instanceof \Symfony\Component\ErrorHandler\Error\FatalError) {
            return response()->json(['status' => 'failed', 'message' => 'Please check Fatal Error.. => ' . $exception->getMessage(), 'code' => $exception->getCode()], 500);
        }
        if ($exception instanceof \Spatie\Permission\Exceptions\UnauthorizedException) {
            return response()->json(['User have not permission for this page access.']);
        }
        if ($exception instanceof \Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException) {
            return response()->json(['status' => 'failed', 'message' => 'Method not allowed'], 405);
        }

        if ($exception instanceof \UnexpectedValueException) {
            return response()->json(['status' => 'failed', 'message' => 'Please check the file permission issue on the folder => ' . $exception->getMessage()], 405);
            \Log::error($exception);
        }

        if ($exception instanceof \Webklex\IMAP\Exceptions\ConnectionFailedException) {
            $email = Email::find($request->route('id'));
            EmailLog::create([
                'email_id' => $email->id,
                'email_log' => 'Error in Sending Email',
                'message' => 'Imap Connection Issue => ' . $exception->getMessage(),
                'is_error' => 1,
                'service_type' => 'IMAP',
            ]);
            $email->error_message = 'Imap Connection Issue => ' . $exception->getMessage();
            $email->save();

            return response()->json(['status' => 'failed', 'message' => 'Imap Connection Issue => ' . $exception->getMessage()], 405);
            \Log::error($exception);
        }

        if ($exception instanceof \Swift_RfcComplianceException) {
            $replymail_id = $request->reply_email_id;
            $email = Email::find($replymail_id);
            EmailLog::create([
                'email_id' => $email->id,
                'email_log' => 'Error in Sending Email',
                'message' => 'Mail Compliance issue Issue => ' . $exception->getMessage(),
                'is_error' => 1,
                'service_type' => 'SMTP',
            ]);
            $email->error_message = 'Mail Compliance issue Issue => ' . $exception->getMessage();
            $email->save();

            return response()->json(['status' => 'failed', 'message' => 'Mail Compliance issue => ' . $exception->getMessage()], 405);
            \Log::error($exception);
        }

        if ($exception instanceof \Swift_TransportException) {
            $replymail_id = $request->reply_email_id;
            if (! is_numeric($replymail_id)) {
                $replymail_id = request()->segment(count(request()->segments()));
            }

            $email = Email::find($replymail_id);
            EmailLog::create([
                'email_id' => $email->id,
                'email_log' => 'Error in Sending Email',
                'message' => 'Mail Transport Issue => ' . $exception->getMessage(),
                'is_error' => 1,
                'service_type' => 'SMTP',
            ]);
            $email->error_message = 'Mail Compliance Issue => ' . $exception->getMessage();
            $email->save();

            return response()->json(['status' => 'failed', 'message' => 'Mail Transport issue => ' . $exception->getMessage()], 405);
            \Log::error($exception);
        }

        if (str_contains($exception->getMessage(), 'Failed to authenticate on SMTP server')) {
            try {
                EmailLog::create([
                    'email_id' => $request->forward_email_id,
                    'email_log' => 'Error in Sending Email',
                    'message' => 'Mail Transport Issue => ' . $exception->getMessage(),
                    'is_error' => 1,
                    'service_type' => 'SMTP',
                ]);
            } catch (Exception $e) {
                return response()->json(['status' => 'failed', 'message' => 'Mail Compliance issue => ' . $exception->getMessage()], 405);
                \Log::error($exception);
            }

            return response()->json(['status' => 'failed', 'message' => 'Mail Compliance issue => ' . $exception->getMessage()], 405);
            \Log::error($exception);
        }

        return parent::render($request, $exception);
    }
}
