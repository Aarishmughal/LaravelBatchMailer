<?php

namespace App\Http\Controllers;

use App\Exports\UsersExport;
use App\Imports\UsersImport;
use App\Models\File;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Maatwebsite\Excel\Facades\Excel;

class UserController extends Controller
{
    public function show()
    {
        $users = User::all();
        return view('show', compact("users"));
    }
    public function edit($id)
    {
        $user = User::find($id);
        return view('edit', compact("user"));
    }
    public function update($id, Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:10',
            'email' => 'required|email|max:25',
        ], [
            'name.required' => 'The name field is required.',
            'name.string' => 'The name must be a string.',
            'name.max' => 'The name may not be greater than :max characters.',

            'email.required' => 'The email field is required.',
            'email.email' => 'The email must be a valid email address.',
            'email.max' => 'The email may not be greater than :max characters.',
        ]);
        $user = User::find($id);
        if ($user) {
            $user->update([
                "name" => $request->name,
                "email" => $request->email,
            ]);
            return redirect()->route('show')->with(["success" => "Entry Updated Successfully!"]);
        } else {
            return redirect()->back()->with(["error" => "Entry could not be Updated!"]);
        }
    }
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:10',
            'email' => 'required|email|max:25',
            'password' => 'required|string|min:6|max:20',
        ], [
            'name.required' => 'Name is required.',
            'name.string' => 'Name must be a string.',
            'name.max' => 'Name may not be greater than :max characters.',

            'email.required' => 'Email is required.',
            'email.email' => 'Email must be a valid email address.',
            'email.max' => 'Email may not be greater than :max characters.',

            'password.required' => 'Password is required.',
            'password.string' => 'Password must be a string.',
            'password.min' => 'Password must be at least :min characters.',
            'password.max' => 'Password may not be greater than :max characters.',
        ]);

        $user = User::create([
            "name" => $request->name,
            "email" => $request->email,
            "password" => Hash::make($request->password),
        ]);
        if ($user) {
            return redirect()->back()->with(["success" => "User Added Successfully!"]);
        } else {
            return redirect()->back()->with(["error" => "User could not be Added!"]);
        }
    }
    public function delete($id)
    {
        $user = User::find($id);
        if ($user) {
            $user->delete();
            return redirect()->route('show')->with(["success" => "Entry Deleted Successfully!"]);
        } else {
            return redirect()->back()->with(["error" => "Entry could not be Deleted!"]);
        }
    }
    public function deleteAll()
    {
        $users = User::all();
        foreach ($users as $user) {
            $user->delete();
        }
        return redirect()->route('show')->with(["success" => "All Entries Deleted Successfully!"]);
    }
    public function mailBatch()
    {
        $users = User::all();
        foreach ($users as $user) {
            try{

                Mail::raw('This is a test email from Excel Handler Mailer!', function ($message) use ($user) {
                    $message->to($user->email)->subject('Test Email');
                });
            }catch(\Exception $e){
                return redirect()->back()->with(["error" => "Email to ".$user->email." Not Sent: ".$e->getMessage()]);
            }
        }
        return redirect()->back()->with(["success" => "Batch Email Sent Successfully!"]);

        // Mail::raw('This is a test email from Laravel!', function ($message) {
        //     $message->to('aarishmughal23@gmail.com')->subject('Test Email');
        // });

        // return redirect()->back()->with(["success" => "Test Email Sent Successfully!"]);
    }
    public function mail($id){
        $user = User::find($id);
        return view('mail', compact("user"));
    }
    public function sendMail($id, Request $request){
        $request->validate([
            'subject' => 'required|string|max:255',
            'body' => 'required|string|max:255',
        ],[
            'subject.required' => 'The subject is required.',
            'subject.string' => 'The subject must be a string.',
            'subject.max' => 'The subject may not be greater than :max characters.',
            
            'body.required' => 'The body is required.',
            'body.string' => 'The body must be a string.',
            'body.max' => 'The body may not be greater than :max characters.',
        ]);

        $user = User::find($id);
        if ($user) {
            Mail::raw($request->body, function ($message) use ($user, $request) {
                $message->to($user->email)->subject($request->subject);
            });
            return redirect()->route('show')->with(["success" => "Email Sent Successfully!"]);
        } else {
            return redirect()->back()->with(["error" => "Email could not be Sent!"])->withInputs($request->all());
        }
    }
    public function upload(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls|max:5120',
        ], [
            'file.required' => 'The file is required.',
            'file.mimes' => 'The file must be a type of: xlsx, xls.',
            'file.max' => 'The file may not be greater than 5 MB.',
        ]);

        $file = $request->file('file');

        if ($file->isValid()) {
            $path = $file->store('users');
            File::create([
                "name" => $file->getClientOriginalName(),
                'path' => $path
            ]);

            Excel::import(new UsersImport, $request->file('file'));

            return redirect()->route('show')->with(["success" => "Data Imported Successfully!"]);
        } else {
            return redirect()->back()->with(["error" => "File could not be Uploaded!"]);
        }
    }
    public function export()
    {
        return Excel::download(new UsersExport, 'Users.xlsx');
    }
}
