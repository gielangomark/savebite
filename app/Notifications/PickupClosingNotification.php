<?php
namespace App\Notifications;
use Illuminate\Notifications\Notification;
class PickupClosingNotification extends Notification { public function __construct(private $order) {} public function via(object $n): array{return ['database','mail'];} public function toArray(object $n): array{return ['title'=>'Waktu pengambilan hampir habis','message'=>'Pesanan '.$this->order->booking_code.' akan kedaluwarsa dalam kurang dari 30 menit.'];} public function toMail(object $n){return (new \Illuminate\Notifications\Messages\MailMessage)->subject('Waktu pengambilan hampir habis')->line('Pesanan '.$this->order->booking_code.' akan segera ditutup.');} }
