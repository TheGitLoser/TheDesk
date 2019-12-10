<?php

namespace App\Http\Controllers;

use App\Models\Auth;
use App\Models\User;
use Illuminate\Http\Request;


class TemplateController extends Controller
{
    
   public function test(Request $request)
   {
//     $request->path();
//     if ($request->is('admin/*')) {}
//     // Without Query String...
// $url = $request->url();
// // With Query String...
// $url = $request->fullUrl();
// if ($request->isMethod('post')) {}

// $name = $request->input('name', 'Sally');   // ->input()    FORM
// $name = $request->query('name', 'Helen');   // ->query();   GET
// $request -> request -> all();               //              POST
// $name = $request->name;
// $input = $request->only('username', 'password');

// $input = $request->except(['credit_card']);

//         echo $request->all();
//         echo $request->only('');
//         echo $request->all();
//         echo $request->all();
//         echo $request->all();
//          $request->post(); // DI
        $input = $request->all();

        $user = User::select('unique_id', 'type', 'email', 'password', 'status')
                    -> where('email', 'test@example.com')
                    -> where('status', '1')
                    ->first()->toArray();

                
         $user = User::select('unique_id', 'name', 'display_id')
         -> where('type', 'indi')
         -> where('status', '1')
         ->get();
        //dd($user);
        $count = $user->toArray();
 
        $result = DB::select('SELECT count(*) as count FROM contact_list where user_id = :user_id 
                                and contact_user_id = (
                        SELECT id FROM user WHERE unique_id = :contact_user_id ) 
                        and status = 1',
                        ['user_id' => $myId, 'contact_user_id' => $unique_id]);


      
      //  $user = $count['display_id'];
        // $user = $user[0]['email'];  if not get first row only

       // return response()->json($test);
       // return view('projects.show', compact('project', 'technologies'));
       $output = $request->session()->all();
       
       // $request->session()->has('users')  exists
       $request->session()->put('user.auth', 'indi');
       $output = $request->session()->get('user.info.unique_id');
       dd($output);
       
       // $request->session()->forget(['key1', 'key2']);
    //   $request->session()->flush();
       
       $session = $request->session()->all();
      //  dd($session);

$count = userTypeAccess($request, ['indi']);

       return view("test") -> with(compact('user', 'count', 'output','session'));
       return response()->json(compact('user', 'count', 'output','session'));
   }
}
