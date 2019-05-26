<?php

namespace App\Http\Controllers;

use App\Dubbizle;
use App\Helpers;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DubbizleController extends Controller
{
    public function index() {
        // $posts = Dubbizle::all();

        $posts = DB::select('
                    SELECT *,
     							 (SELECT mm3.id FROM chat_messages mm3 WHERE mm3.id = message_id) AS message_id,
                    (SELECT mm1.message FROM chat_messages mm1 WHERE mm1.id = message_id) as message,
                    (SELECT mm2.status FROM chat_messages mm2 WHERE mm2.id = message_id) AS message_status,
                    (SELECT mm4.sent FROM chat_messages mm4 WHERE mm4.id = message_id) AS message_type,
                    (SELECT mm2.created_at FROM chat_messages mm2 WHERE mm2.id = message_id) as last_communicated_at

                    FROM (
                      SELECT * FROM dubbizles

                      LEFT JOIN (SELECT MAX(id) as message_id, dubbizle_id, message, MAX(created_at) as message_created_At FROM chat_messages WHERE chat_messages.status != 7 AND chat_messages.status != 8 AND chat_messages.status != 9 GROUP BY dubbizle_id ORDER BY chat_messages.created_at DESC) AS chat_messages
                      ON dubbizles.id = chat_messages.dubbizle_id

                    ) AS dubbizles
                    WHERE id IS NOT NULL
                    ORDER BY last_communicated_at DESC;
     						');

                // dd($posts);

        return view('dubbizle', compact('posts'));
    }

    public function show($id)
    {
      $dubbizle = Dubbizle::find($id);
      $users_array = Helpers::getUserArray(User::all());

      return view('dubbizle-show', [
        'dubbizle'  => $dubbizle,
        'users_array'  => $users_array,
      ]);
    }
}
