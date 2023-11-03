<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Services\ImageService;
use App\Traits\ImageUploadTrait;
use Illuminate\Http\Request;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Hash; //Added By IT20179076 - Import Hash for secure password hashing

class AdminAuthController extends Controller
{
    use ImageUploadTrait;

    public function login(): View
    {
        return view('backend.login');
    }

    public function forgotPassword(): View
    {
        return view('backend.forgot-password');
    }

    public function accountSetting(): View
    {
        return view('backend.account_setting');
    }

    public function updateAccount(Request $request): RedirectResponse
    {

    //Added By IT20179076
        // Implement request validation to ensure secure user input.
        $validatedData = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'username' => 'required|string|max:255',
            'email' => 'required|string|email|max:255',
            'phone' => 'nullable|string|max:255',
            'status' => 'required|boolean',
            'user_image' => 'image|mimes:jpeg,png,jpg,gif|max:2048', // Validate and sanitize image upload.
            'password' => 'nullable|string|min:8', // Validate and secure password input.
        ]);

        $adminImage = auth()->user()->user_image; // Default value if no new image is uploaded.
        if ($request->hasFile('user_image')) {
            if (auth()->user()->user_image) {
                (new ImageService())->unlinkImage(auth()->user()->user_image, 'users');
            }
            $adminImage = (new ImageService())->storeUserImages(auth()->user()->username, $request->user_image);
        }

        $password = auth()->user()->password; //Added By IT20179076 -  Default value if no new password is provided.
        if ($request->password){
            if ($request->password) {
            $password = Hash::make($request->password); //Added By IT20179076 - Secure password hashing.
        }


        //Added By IT20179076-  Update user information using validated and secure data.
        auth()->user()->update([
            'first_name' => $validatedData['first_name'],
            'last_name' => $validatedData['last_name'],
            'username' => $validatedData['username'],
            'email' => $validatedData['email'],
            'phone' => $validatedData['phone'],
            'status' => $validatedData['status'],
            'user_image' => $adminImage,
            'password' => $password,
        ]);


        return redirect()->route('admin.index')->with([
            'message' => 'Updated successfully',
            'alert-type' => 'success'
        ]);
    }
}
