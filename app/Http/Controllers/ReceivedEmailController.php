<?php

namespace App\Http\Controllers;

use App\Models\ReceivedEmail;
use App\Models\Provider;
use Illuminate\Http\Request;

class ReceivedEmailController extends Controller
{
    /**
     * Display a listing of received emails
     */
    public function index(Request $request)
    {
        $query = ReceivedEmail::with('provider')
            ->orderBy('received_at', 'desc');
            
        // Filtrado por proveedor
        if ($request->has('provider_id') && $request->provider_id) {
            $query->where('provider_id', $request->provider_id);
        }
        
        // Filtrado por estado de lectura
        if ($request->has('is_read')) {
            $query->where('is_read', $request->is_read == '1');
        }
        
        // Búsqueda por asunto o remitente
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('subject', 'like', "%{$search}%")
                  ->orWhere('sender_email', 'like', "%{$search}%")
                  ->orWhere('sender_name', 'like', "%{$search}%");
            });
        }
        
        // Filtrado por fecha
        if ($request->has('date_start') && $request->date_start) {
            $query->whereDate('received_at', '>=', $request->date_start);
        }
        
        if ($request->has('date_end') && $request->date_end) {
            $query->whereDate('received_at', '<=', $request->date_end);
        }
        
        $emails = $query->paginate(15);
        $providers = Provider::where('active', true)->get();
        
        return view('emails.received.index', compact('emails', 'providers'));
    }
    
    /**
     * Display the specified received email
     */
    public function show(ReceivedEmail $receivedEmail)
    {
        // Marcar como leído si aún no lo está
        if (!$receivedEmail->is_read) {
            $receivedEmail->update(['is_read' => true]);
        }
        
        return view('emails.received.show', compact('receivedEmail'));
    }
    
    /**
     * Mark an email as read/unread
     */
    public function toggleRead(ReceivedEmail $receivedEmail)
    {
        $receivedEmail->update(['is_read' => !$receivedEmail->is_read]);
        
        return redirect()->back()->with('success', 
            $receivedEmail->is_read ? 'Correo marcado como leído' : 'Correo marcado como no leído');
    }
    
    /**
     * Delete a received email
     */
    public function destroy(ReceivedEmail $receivedEmail)
    {
        $receivedEmail->delete();
        
        return redirect()->route('received-emails.index')
            ->with('success', 'Correo eliminado correctamente');
    }
    
    /**
     * Batch operations (mark as read, delete)
     */
    public function batch(Request $request)
    {
        $validated = $request->validate([
            'email_ids' => 'required|array',
            'email_ids.*' => 'exists:received_emails,id',
            'action' => 'required|in:mark_read,mark_unread,delete'
        ]);
        
        $count = count($validated['email_ids']);
        
        switch($validated['action']) {
            case 'mark_read':
                ReceivedEmail::whereIn('id', $validated['email_ids'])
                    ->update(['is_read' => true]);
                $message = "{$count} correos marcados como leídos";
                break;
                
            case 'mark_unread':
                ReceivedEmail::whereIn('id', $validated['email_ids'])
                    ->update(['is_read' => false]);
                $message = "{$count} correos marcados como no leídos";
                break;
                
            case 'delete':
                ReceivedEmail::whereIn('id', $validated['email_ids'])->delete();
                $message = "{$count} correos eliminados correctamente";
                break;
        }
        
        return redirect()->back()->with('success', $message);
    }
}