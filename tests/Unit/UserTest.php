<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class UserTest extends TestCase
{
    /**
     * Test Show All User
     *
     * @return void
     */
    public function test_show_all_user()
    {        
        $users = [];
        $response = $this->get('/');
        
        $response->assertStatus(200);
        $response->assertViewIs('user.index');
        $response->assertViewHasAll($users);
    }

    /**
     * Test Create User
     *
     * @return void
     */
    public function test_open_form_create_user()
    {
        $response = $this->get('/create');
        $response->assertStatus(200);

        $response->assertSessionHas('update', false);
        $response->assertSessionHas('old_txt', '');
        $response->assertViewIs('user.create');
    }

    /**
     * Test Edit User
     *
     * @return void
     */
    public function test_open_form_edit_user()
    {
        $files = glob(storage_path('app/public/user/*.txt'));
        
        if (count($files) > 0) {
            
            $fileTxt = substr($files[0], strpos($files[0], 'user/'));
            $key      = str_replace(['user/', '.txt'], '', $fileTxt);

            $data = explode(',', (String) File::get($files[0]));

            $user = [
                'name'    => $data[0],
                'email'   => $data[1],
                'birth'   => $data[2],
                'phone'   => $data[3],
                'gender'  => $data[4],
                'address' => $data[5]
            ];

            $response = $this->get('/edit/' . $key);
            
            $response->assertStatus(200);
            $response->assertSessionHas('update', true);
            $response->assertSessionHas('old_txt', $fileTxt);
            $response->assertViewHas('user', $user);
            $response->assertViewIs('user.edit');

        }else{
            dump('__No Data to Test__');
        }
    }

    public function test_store_user_must_pass_validation()
    {
        $response = $this->post('/store', [
            'name'    => '',
            'email'   => '',
            'birth'   => '',
            'phone'   => '',
            'gender'  => '',
            'address' => '',
        ]);

        $response->assertSessionHasErrors(['name', 'email', 'birth', 'phone', 'gender', 'address']);
        
        $response->assertRedirect('/');
    }

    /**
     * Test Delete User
     *
     * @return void
     */
    public function test_delete_user()
    {
        $files = glob(storage_path('app/public/user/*.txt'));
        
        if (count($files) > 0) {
            
            $fileTxt = substr($files[0], strpos($files[0], 'user/'));

            $key      = str_replace(['user/', '.txt'], '', $fileTxt);

            $response = $this->delete('/delete/' . $key);

            $response->assertRedirect('/');

        }else{
            dump('__No Data to Test__');
        }
    }

}
