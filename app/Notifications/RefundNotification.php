<?php
namespace App\Notifications;
use Illuminate\Notifications\Notification;
class RefundNotification extends Notification { public function __construct(private $order) {} public function via(object $n): array{return ['database','mail'];} public function toArray(object $n): array{return ['title'=>'Refund saldo berhasil','message'=>'Rp '.number_format($this->order->total_price).' masuk ke saldo Save Bite.'];} public function toMail(object $n){return (new \Illuminate\Notifications\Messages\MailMessage)->subject('Refund Save Bite berhasil')->line('Refund Rp '.number_format($this->order->total_price).' sudah masuk ke saldo Anda.');} }
