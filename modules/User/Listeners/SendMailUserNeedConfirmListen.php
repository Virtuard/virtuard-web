<?php

    namespace Modules\User\Listeners;

    use Illuminate\Support\Facades\Mail;
    use Modules\User\Events\SendMailUserNeedConfirm;
    use Modules\User\Models\User;

    class SendMailUserNeedConfirmListen
    {
        /**
         * Create the event listener.
         *
         * @return void
         */
        public $user;

        public function __construct(User $user)
        {
            $this->user = $user;
        }

        /**
         * Handle the event.
         *
         * @param Event $event
         * @return void
         */
        public function handle(SendMailUserNeedConfirm $event)
        {
            $event->user->sendEmailUserNeedConfirmNotification();
        }

    }
