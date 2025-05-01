<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SupportTicket;
use App\Models\SupportMessage;
use App\Models\SupportFile;
use App\Rules\General\FileExtentionCheckRule;
use App\Rules\SupportTicketFileCheckRule;

class SupportTicketController extends Controller
{
    public function create()
    {
        $title = translate('Create New ticket');
        return view('user.create_ticket', compact('title'));
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => 'required|max:255',
            'email' => 'required|email',
            'subject' => 'required|max:255',
            'file.*' => [new FileExtentionCheckRule(['pdf','doc','exel','jpg','jpeg','png','jfif','webp'],'file')]
        ]);
        $supportTicket = new SupportTicket();
        $supportTicket->ticket_number = random_number();
        $supportTicket->user_id = auth()->user()->id ?? null;
        $supportTicket->name = $request->name;
        $supportTicket->email = $request->email;
        $supportTicket->subject = $request->subject;
        $supportTicket->priority = 2;
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
                    return  back()->with('error',translate("Could not upload your file"));
                }
            }
        }
        return  redirect()->back()->with('success',translate("Support ticket has been created"));
    }

    public function view($ticketNumber)
    {
        $title = translate('Support ticket view');
        $ticket = SupportTicket::with(['messages','messages.supportfiles'])->where('ticket_number',$ticketNumber)->with(['messages','messages.supportfiles'])->firstOrFail();
        return view('frontend.ticket_view', compact('title', 'ticket'));
    }

    public function ticketReply(Request $request, $id)
    {
        $request->validate([
            'message'=>'required'
        ]);
        $supportTicket = SupportTicket::where('id', $id)->firstOrFail();
        $supportTicket->user_id  = auth()->user()->id ?? null;
        $supportTicket->status = 3;
        $supportTicket->save();

        $message = new SupportMessage();
        $message->support_ticket_id = $supportTicket->id;
        $message->message = $request->message;
        $message->save();
        if($request->hasFile('file')){
            try {
                $supportFile = new SupportFile();
                $supportFile->support_message_id = $message->id;
                $supportFile->file = upload_new_file($request->file, file_path()['ticket']['path']);
                $supportFile->save();
            } catch (\Exception $exp) {
                return  back()->with('error',translate("Could not upload your file"));
            }
        }
        return  back()->with('success',translate("Support ticket replied successfully"));
    }


    public function closedTicket($id)
    {
        $supportTicket =  SupportTicket::where('ticket_number', decrypt($id))->firstOrFail();
        $supportTicket->status = 4;
        $supportTicket->save();
        return  back()->with('success',translate("Support ticket has been closed"));
    }


    public function supportMessageDelete($id)
    {
        $supportMessage =  SupportMessage::where('id', decrypt($id))->firstOrFail();
        if($supportMessage->supportfiles->isNotEmpty()){
            foreach($supportMessage->supportfiles as $supportFile){
                $location = file_path()['ticket']['path'];
                if(file_exists($location.'/'.$supportFile->file) && is_file($location.'/'.$supportFile->file)){
                    @unlink($location.'/'.$supportFile->file);
                }
                $supportFile->delete();
            }
        }
        $supportMessage->delete();
        return  back()->with('success',translate("Support ticket has been closed"));

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
