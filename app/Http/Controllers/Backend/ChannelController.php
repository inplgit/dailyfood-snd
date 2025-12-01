<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Channel;

class ChannelController extends Controller
{
    public function index()
    {
     $channels = Channel::where('status', 1)->get();

        return view('pages.channels.index', compact('channels'));
    }

    public function create()
    {
        return view('pages.channels.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
          
        ]);

        Channel::create($request->all());
        return redirect()->route('channels.index')->with('success', 'Channel added successfully!');
    }

    public function edit(Channel $channel)
    {
        return view('pages.channels.edit', compact('channel'));
    }

    public function update(Request $request, Channel $channel)
    {
        $request->validate([
            'name' => 'required',
       
        ]);

        $channel->update($request->all());
        return redirect()->route('channels.index')->with('success', 'Channel updated successfully!');
    }

    public function destroy(Channel $channel)
    {
        $channel->update(['status' => '0']); 
        return redirect()->route('channels.index')->with('success', 'Channel deleted successfully!');
    }


}
