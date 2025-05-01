<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SupportTicket;
use App\Models\SupportMessage;
use App\Models\SupportFile;



class SupportTicketController extends Controller
{
    public function index()
    {
        $title   = translate('Manage support ticket');
        $seller  = auth()->guard('seller')->user();
        $tickets = SupportTicket::search()->with(['messages'])->whereNotNull('seller_id')->where('seller_id', $seller->id)->latest()->paginate(site_settings('pagination_number',10));
        return view('seller.support.index', compact('title', 'tickets'));
    }

    public function create()
    {
        $title = translate('Create new ticket');
        return view('seller.support.create', compact('title'));
    }


    public function store(Request $request)
    {

        $this->validate($request, [
            'subject'  => 'required|max:255',
            'priority' => 'required|in:1,2,3',
            'message'  => 'required',

        ]);

        $seller = auth()->guard('seller')->user();
        $supportTicket = new SupportTicket();
        $supportTicket->ticket_number = random_number();
        $supportTicket->seller_id = $seller->id;
        $supportTicket->subject = $request->subject;
        $supportTicket->priority = $request->priority;
        $supportTicket->status = 1;
        $supportTicket->save();

        $message = new SupportMessage();
        $message->support_ticket_id = $supportTicket->id;
        $message->admin_id = null;
        $message->message = $request->message;
        $message->save();

        if($request->hasFile('file')) {
            foreach ($request->file('file') as $file) {
                try {
                    $supportFile = new SupportFile();
                    $supportFile->support_message_id = $message->id;
                    $supportFile->file = upload_new_file($file, file_path()['ticket']['path']);
                    $supportFile->save();
                } catch (\Exception $exp) {
                    return back()->with('error',translate("Could not upload your File"));
                }
            }
        }
        return redirect()->route('seller.ticket.index')->with('success',translate("Support ticket has been created"));

    }

    public function detail($id)
    {
        $title = translate('Ticket Reply');
        $seller = auth()->guard('seller')->user();
        $ticket = SupportTicket::with(['messages','messages.supportfiles'])->whereNotNull('seller_id')->where('seller_id', $seller->id)->where('id', $id)->firstOrFail();
        return view('seller.support.detail', compact('title', 'ticket'));
    }


    public function ticketReply(Request $request, $id)
    {
        $seller = auth()->guard('seller')->user();
        $supportTicket = SupportTicket::whereNotNull('seller_id')->where('seller_id', $seller->id)->where('id', $id)->firstOrFail();
        $supportTicket->status = 3;
        $supportTicket->save();

        $message = new SupportMessage();
        $message->support_ticket_id = $supportTicket->id;
        $message->admin_id = null;
        $message->message = $request->message;
        $message->save();
        if ($request->hasFile('file')) {
            foreach ($request->file('file') as $file) {
                try {
                    $supportFile = new SupportFile();
                    $supportFile->support_message_id = $message->id;
                    $supportFile->file = upload_new_file($file, file_path()['ticket']['path']);
                    $supportFile->save();
                } catch (\Exception $exp) {
                    return back()->with('error',translate("Could not upload your File"));
                }
            }
        }
        return back()->with('success',translate("Support ticket replied successfully"));
    }

    public function closedTicket($id)
    {
        $seller = auth()->guard('seller')->user();
        $supportTicket =  SupportTicket::whereNotNull('seller_id')->where('seller_id', $seller->id)->where('id', $id)->firstOrFail();
        $supportTicket->status = 4;
        $supportTicket->save();
        return back()->with('success',translate("Support ticket has been closed"));
    }


    public function supportTicketDownlode($id)
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
