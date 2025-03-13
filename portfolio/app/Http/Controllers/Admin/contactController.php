<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Contact;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class contactController extends Controller
{
    public function index(Request $request)
    {
        $size = $request->input('size', 10);
        $contacts = Contact::paginate($size);
        return view("admin.Contact.contact", compact('contacts'));
    }
    public function showSendForm($id)
    {
        $contact = Contact::findOrFail($id);
        return view("admin.Contact.sendContact", compact('contact'));
    }

    // Xử lý gửi mail và cập nhật trạng thái
    public function sendContact(Request $request, $id)
    {
        $contact = Contact::findOrFail($id);

        // Dữ liệu cố định từ hệ thống
        $emailData = [
            'name' => 'MyPortfolio',
            'email' => 'MyPortfolio@gmail.com',
            'subject' => 'Reply from MyPortfolio',
            'messageContent' => $request->input('message'), // Chỉ nhận message từ form
        ];

        // Gửi email
        Mail::send('emails.adminSendContact', ['data' => $emailData], function ($mail) use ($contact) {
            $mail->to($contact->email)
                ->subject('Reply from MyPortfolio');
        });

        // Cập nhật trạng thái thành 'processed'
        $contact->update(['status' => 'processed']);

        return redirect()->route('admin.contact.index')->with('success', 'Email sent successfully!');
    }



}
