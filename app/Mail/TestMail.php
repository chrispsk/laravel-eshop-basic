<?php

namespace App\Mail;
use App\Cart;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class TestMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public $content; //$cart
    public function __construct($bdy) //Cart $cart
    {
        $this->content = $bdy; //$cart
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        // don't need with if I use Cart object 
        return $this->view('mail')->with(['content'=>$this->content]); 
    }
}
