<?php

namespace App\Http\Controllers;

use Chumper\Zipper\Facades\Zipper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{    
    /**
     * Show User's List
     *
     * @param  Request (Optional)
     * @return view
     * @author Sam Muza
     **/
    public function index(Request $request)
    {
        // Get All Files from "user" Folder from Storage Public
        $files = Storage::files('user');

        // Sort By Date DESC
        $files = array_reverse(array_sort($files, function($value){
            return Storage::lastModified($value);
        }));
        
        // Initial Value
        $users = [];
        
        // Get Content of each of file to Array & insert to $users
        foreach($files as $file) {

            // Key => "Musa-20181201093015" (for edit / delete)
            $key  = str_replace(['user/', '.txt'], '', $file); 

            // Get User data From [file.txt]
            $txt = explode(",", Storage::get($file));

            // data each user
            $user = [
                'key'     => $key,
                'name'    => $txt[0],
                'email'   => $txt[1],
                'birth'   => $txt[2],
                'phone'   => $txt[3],
                'gender'  => $txt[4],
                'address' => $txt[5]
            ];

            // insert into $users[]
            $users[] = $user;
        }

        // Filter Data if Search Query Exists
        if ($request->search) {
            
            $users = array_filter($users, function($value) use($request) {
                // remove key
                array_shift($value);
                
                // convert to Text
                $dataText = implode(',', $value);

                return stripos($dataText, $request->search) !== false;
            });
        }


        return view('user.index', compact('users'));
    }

    /**
     * Create new User / Show Form Add User
     *
     * @return view
     * @author Sam Muza
     */
    public function create()
    {
        session(['update' => false, 'old_txt' => '']);
        return view('user.create');
    }

    /**
     * Store to txt File
     *
     * @param  Request 
     * @return Redirect => user.index
     * @author Sam Muza
     **/
    public function store(Request $request)
    {
        try {

            // Run Validator
            $valid = $this->checkInputIsValid($request);

            // Check If Data InValid 
            if ($valid->fails()) {
                return redirect()->back()->withErrors($valid)->withInput();
            }

            // Format FileName (Remove Space & Char [,])
            $fileName = $this->remove_space_comma($request->name)  . '-' . date('dmYHis') . '.txt';

            // remove space & char [,] from Request 
            foreach ($request->except('_token') as $value) {
                $user[] =  $this->remove_space_comma($value);
            }

            // Save Data (Write File)
            if (Storage::put('user/' . $fileName, implode($user, ","))) {
                
                $info = 'User Successfully Added';

                // If "UPDATE" data, then Delete Old File [file.txt]
                if (session('update')) {
                    Storage::delete(session('old_txt'));
                    $info = 'User Successfully Updated';
                }

            }else{
                // If Failed write File
                $info = 'Failed to Create / Update User';
            }
            
            return redirect('/')->with('info', $info);

        } catch (\Exception $e) {
            return redirect('/')->with('info',  $e->getMessage());
        }
    }

    /**
     * Edit User Detail's
     *
     * @param  user file (filename.txt)
     * @return view
     * @author Sam Muza
     **/
    public function edit($filename)
    {
        $file = 'user/' . $filename . '.txt';

        if (Storage::exists($file)) {

            // Get data from file.txt
            $txt = explode(",", Storage::get($file));

            // assign to $user array
            $user = [
                'name'    => $txt[0],
                'email'   => $txt[1],
                'birth'   => $txt[2],
                'phone'   => $txt[3],
                'gender'  => $txt[4],
                'address' => $txt[5]
            ];
            
            // set operation as "update" & remember "old_txt"
            session([
                'update'   => true, 
                // 'old_data' => array('fileTxt' => $file, 'email' => $txt[1], 'phone' => $txt[3]), 
                'old_txt' => $file, 
            ]);

            return view('user.edit', compact('user'));

        }else{
            return redirect('/')->with('info', 'File "' . $filename . '" Not Found !');
        }
    }

    /**
     * Delete User
     *
     * @param  user file (filename.txt)
     * @return Redirect user.index
     * @author Sam Muza
     **/
    public function delete($filename)
    {
        try {
        
            $file = 'user/' . $filename . '.txt';

            // Check File Exists
            if (Storage::exists($file)) {
                
                // Delete Data (Delete File)
                if (Storage::delete($file)) {
                    $info = 'User Successfully Deleted';
                }else{
                    $info = 'Failed to Delete User';
                }

                return redirect('/')->with('info', $info);

            }else{
                return redirect('/')->with('info', 'File "' . $filename . '" Not Found !');
            }

        } catch (\Exception $e) {
            return redirect('/')->with('info',  $e->getMessage());
        }
    }

    /**
     * Download All txt Files from "storage/public/user" to Zip
     *
     * @return Response download
     * @author Sam Muza
     **/
    public function backup()
    {
        try {

            // Get All Files [.txt]
            $files    = glob(storage_path('app/public/user/*.txt'));

            $fileName = 'Backup (' . count($files) . ' Users) ' . date('[d F Y] [H-i-s]') .'.zip';

            // temp file Path for make Zip
            $source   = public_path($fileName);

            // Run Backup Only When Data Exists
            if (count($files) > 0) {
                
                // Make Zipe File (Data From $files)
                Zipper::make($source)->add($files)->close();
                
                // Run Download
                return response()->download($source)->deleteFileAfterSend(true);

            }else{
                return redirect('/')->with('info', 'No User Data to Backup');
            }

        } catch (\Exception $e) {
            return redirect('/')->with('info',  $e->getMessage());
        }
    }

    /**
     * Restore Data (Extract Zip) to "storage/public/user"
     *
     * @param  Request
     * @return Redirect
     * @author Sam Muza
     **/
    public function restore(Request $request)
    {
        try {

            // uploaded File
            $upload = $request->file('upload');
            
            // Check Uploaded File Exists
            if ($upload) {

                $valid = $this->checkFileIsValid($request);

                if ($valid->fails()) {
                    return redirect('/')->with('info', $valid->errors()->first('upload'));
                }
                
                // Remove All user first
                $this->removeAllUser();

                // Extract Files
                Zipper::make($upload)->extractTo(storage_path('app/public/user'));

                $info = "Restore Data Success";

            }else{
                $info = "Restore Data Failed, Please Upload File Again";
            }

            return redirect('/')->with('info', $info);

        } catch (\Exception $e) {
            return redirect('/')->with('info',  $e->getMessage());
        }
    }

    /**
     * Reset Data (Delete All File)
     *
     * @return Redirect
     * @author Sam Muza
     **/
    public function reset()
    {
        if ($this->removeAllUser()) {
            $info = 'Reset Data Success';
        }else{
            $info = 'No User Data to Reset';
        }

        return redirect('/')->with('info', $info);
    }

    /**
     * Remove Data (Delete All File) from "storage/public/user"
     *
     * @return Boolean
     * @author Sam Muza
     **/
    public function removeAllUser()
    {
        try {
            
            // Get All Data
            $files = Storage::files('user');

            // IF File Exists Run Delete
            if (count($files) > 0) {
                foreach ($files as $file) {
                    Storage::delete($file);
                }
                return true;
            }
            
            return false;

        } catch (\Exception $e) {
            return redirect('/')->with('info',  $e->getMessage());
        }
    }

    /**
     * Check Input is Valid
     *
     * @param  Request
     * @return Validator
     * @author Sam Muza
     **/
    public function checkInputIsValid(Request $request)
    {
        // Custom Error Message
        $messages = [
            'required'              => 'We need your :Attribute ( Must Be Filled ) !',
            'min'                   => ':Attribute ( Must be Filled More than :min Characters ) !',
            'email.email'           => 'Email Must Valid (Must be Formatted as an E-mail Address) ! (ex: cholikul@gmail.com)',
            'birth.date_format'     => 'Date of Birth Required Corresponding Date Format (Month / Day / Year) !  ! (ex: 09/24/1990)',
            'birth.before_or_equal' => 'Date of Birth Must be a Date Before or Equal Today !',
            'phone.numeric'         => 'Phone Number Only Allow Numeric Character (ex: 087759625462) !',
            'phone.digits_between'  => 'Phone Number Only Allow :min - :max Character (ex: 087759625462) !',
            'gender.in'             => 'You Must Choose Gender between Male Or Female !',
        ];

        // Validation Rule
        $rules = [
            'name'    => 'required|min:3',
            'email'   => 'required|email',
            'birth'   => 'required|date_format:"Y-m-d"|before_or_equal:today',
            'phone'   => 'required|numeric|digits_between:6,13',
            'gender'  => 'required|in:Male,Female',
            'address' => 'required|min:3',
        ];

        // Run Validator
        $validator = Validator::make($request->all(), $rules, $messages);

        // Custom validator to Check Unique Email & Phone 
        $validator->after(function($validator) use($request) {
            
            // Detect Error
            $isError = false;

            // Get Existing Email and Phone
            $data = $this->get_existing_email_phone();

            $email_list = $data['email_list'];
            $phone_list = $data['phone_list'];

            if (in_array($request->email, $email_list)) {
                $validator->errors()->add('email', 'Email is Already Taken !');
            }

            if (in_array($request->phone, $phone_list)) {
                $validator->errors()->add('phone', 'Phone is Already Taken !');
            }
            
        });

        return $validator;
    }

    /**
     * Get Email & Phone Already Exists
     *
     * @return Array
     * @author Sam Muza
     **/
    public function get_existing_email_phone()
    {
        $email_list = [];
        $phone_list = [];

        // Get All Txt Files
        $files = Storage::files('user');
        
        foreach ($files as $file) {
            
            // If "old_txt" exists (it's mean Update) => Exclude "Current file"
            if ($file != session('old_txt')) {

                $txt  = Storage::get($file);
                $user = explode(',', $txt);

                $email_list[] = $user[1];
                $phone_list[] = $user[3];
            }
        }   
      
        return array('email_list' => $email_list, 'phone_list' => $phone_list);
    }

    /**
     * Check Uploaded File (Restore) Valid
     *
     * @param  Request
     * @return Validator
     * @author Sam Muza
     **/
    public function checkFileIsValid(Request $request)
    {        
        // Make Validation
        $validator = Validator::make($request->all(), [
            'upload' => 'mimes:zip'
        ]);

        // Stop validator if first rule is Fail
        if ($validator->fails()) {
            return $validator;
        }

        // Custom Rule to check File Uploaded is Valid
        $validator->after(function($validator) use($request) {
            
            // temporary folder path
            $tempPath = storage_path('app/public/temp');

            // Extract Files
            Zipper::make($request->file('upload'))->extractTo($tempPath);
            
            // Get All File [.txt]
            $files = glob($tempPath . '/*.txt');
            
            // Check Number of Files
            if (count($files) > 0) {

                // detect Error
                $isError = false;

                // Check each of files
                foreach ($files as $file) {
                    
                    // txt file
                    $txt = File::get($file);

                    // File is Valid if Contain User Data (6 Fields)
                    if (count(explode(",", $txt)) != 6) {
                        $isError = true;
                        $info = 'File is Invalid, Try Again !';
                        break;
                    }
                }

            }else{
                $isError = true;
                $info = 'File is Empty (No User Data Found)';
            }

            // Add error if $isError == true
            if ($isError) {
                $validator->errors()->add('upload', $info);
            }

            // Delete All files on "storage/app/public/temp"
            $files = glob($tempPath . '/*');
                        
            // Delete File if "temp" folder not empty
            if (count($files) > 0) {
                
                // Delete each of Folder & files
                foreach ($files as $file) {
                    if(is_dir($file)) {
                        File::deleteDirectory($file);
                    }else{
                        File::delete($file);
                    }
                }
            }

        });

        return $validator;
    }

    /**
     * Remove Left, Right Space & char [,]
     *
     * @param  String
     * @return String
     * @author Sam Muza
     **/
    public function remove_space_comma($string)
    {
        return trim(str_replace(',', '', $string));
    }
}