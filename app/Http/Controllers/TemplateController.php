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

                
        // $count = $user -> count();
        $count = 'a';

      
        $user = $user['email'];
        // $user = $user[0]['email'];  if not get first row only

       // return response()->json($test);
       // return view('projects.show', compact('project', 'technologies'));
       $output = $request->session()->all();
       
       // $request->session()->has('users')  exists
       $request->session()->put('user.auth', 'indi');
       $output = $request->session()->get('user');
       
       // $request->session()->forget(['key1', 'key2']);
       $request->session()->flush();
       
       $session = $request->session()->all();

$count = userTypeAccess($request, ['indi']);

       return view("test") -> with(compact('user', 'count', 'output','session'));
       return response()->json(compact('user', 'count', 'output','session'));
   }
}
