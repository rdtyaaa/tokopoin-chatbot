<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SupportTicket;
use App\Models\SupportFile;
use App\Models\SupportMessage;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class SupportTicketController extends Controller

{

    public function __construct(){
        $this->middleware(['permissions:view_support'])->only('index','running','answered','replied','closed','ticketDetails'.'supportTicketDownlode','ticketReply');
        $this->middleware(['permissions:update_support'])->only('closedTicket','ticketReply');
    }
    public function index() :View
    {
        $title          = translate('Manage Support ticket');
        $supportTickets = SupportTicket::search()->with(['user','seller','messages','deliveryMan'])->latest()->paginate(site_settings('pagination_number',10))->appends(request()->all());
        return view('admin.support_ticket.index', compact('title', 'supportTickets'));
    }

    public function running() :View
    {
        $title          = translate('Manage running support ticket');
        $supportTickets = SupportTicket::search()->with(['user','seller','messages','deliveryMan'])->where('status', 1)->latest()->paginate(site_settings('pagination_number',10))->appends(request()->all());
        return view('admin.support_ticket.index', compact('title', 'supportTickets'));
    }

    public function answered():View
    {
        $title          = translate('Manage answered support ticket');
        $supportTickets = SupportTicket::search()->with(['user','seller','messages','deliveryMan'])->where('status', 2)->latest()->paginate(site_settings('pagination_number',10))->appends(request()->all());
        return view('admin.support_ticket.index', compact('title', 'supportTickets'));
    }

    public function replied():View
    {
        $title          = translate('Manage replied support ticket');
        $supportTickets = SupportTicket::search()->with(['user','seller','messages','deliveryMan'])->where('status', 3)->latest()->paginate(site_settings('pagination_number',10))->appends(request()->all());
        return view('admin.support_ticket.index', compact('title', 'supportTickets'));
    }

    public function closed():View
    {
        $title          = translate('Manage closed support ticket');
        $supportTickets = SupportTicket::search()->with(['user','seller','messages','deliveryMan'])->where('status', 4)->latest()->paginate(site_settings('pagination_number',10))->appends(request()->all());
        return view('admin.support_ticket.index', compact('title', 'supportTickets'));
    }

    public function ticketDetails(int $id):View
    {
        $title         = translate('Support ticket reply');
        $supportTicket = SupportTicket::with(['user','seller','messages','messages.supportfiles','deliveryMan'])->orderBy('created_at','ASC')->findOrFail($id);
        return view('admin.support_ticket.details', compact('title', 'supportTicket'));
    }

    public function ticketReply(Request $request, int $id) :RedirectResponse
    {

        $supportTicket = SupportTicket::findOrFail($id);
        $supportTicket->status = 2;
        $supportTicket->save();

        $message                       = new SupportMessage();
        $message->support_ticket_id    = $supportTicket->id;
        $message->admin_id             = Auth::guard('admin')->id();
        $message->message              = $request->message;
        $message->save();
        if ($request->hasFile('file')) {
            foreach ($request->file('file') as $file) {
                try {
                    $supportFile                     = new SupportFile();
                    $supportFile->support_message_id = $message->id;
                    $supportFile->file               = upload_new_file($file, file_path()['ticket']['path']);
                    $supportFile->save();
                } catch (\Exception $exp) {
                }
            }
        }

        return back()->with('success',translate('Support ticket replied successfully'));
    }


    public function closedTicket(int $id) :RedirectResponse
    {
        $supportTicket = SupportTicket::findOrFail($id);
        $supportTicket->status = 4;
        $supportTicket->save();

        return back()->with('success',translate('Support ticket has been closed'));
    }

    public function supportTicketDownlode(string $id) :mixed
    {

        $supportFile = SupportFile::findOrFail(decrypt($id));
        $file = $supportFile->file;
        $path = file_path()['ticket']['path'].'/'.$file;
        $title = make_slug('file').'-'.$file;
        $mimetype = mime_content_type($path);
        header('Content-Disposition: attachment; filename="' . $title);
        header("Content-Type: " . $mimetype);
        return readfile($path);
    }


}

