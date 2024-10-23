<?php

namespace App\Events;

use App\Http\Resources\PostResource;
use App\Models\Post;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class NewPostEvent implements ShouldBroadcastNow
{
	use Dispatchable, InteractsWithSockets, SerializesModels;
	
	
	
	public function __construct(
		public Post $post,
		)
		{}
		
		/**
		* Get the channels the event should broadcast on.
		*
		* @return array<int, \Illuminate\Broadcasting\Channel>
		*/
		public function broadcastOn(): array
		{
			return [
				new Channel('post'),
			];
		}
		
		public function broadcastAs(): string
		{
			return 'created';
		}
		
		public function broadcastWith(): array
		{
			return ['post' => new PostResource($this->post)];
		}
	}
	