<?php
namespace App\Notifications;
use Illuminate\Notifications\Notification;
class MenuActivationReminder extends Notification { public function __construct(private $product) {} public function via(object $n): array{return ['database','mail'];} public function toArray(object $n): array{return ['title'=>'Aktifkan menu sebelum jam pengambilan','message'=>$this->product->title.' masih nonaktif dan waktu pickup segera dimulai.'];} public function toMail(object $n){return (new \Illuminate\Notifications\Messages\MailMessage)->subject('Aktifkan menu Save Bite')->line($this->product->title.' masih nonaktif.');} }
