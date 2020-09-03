<?php

namespace App\Listeners\Finance;

use App\Events\Finance\TranstrakParse;
use App\Http\Controllers\Ajax\Finance\Transtrak;
use App\Models\User;
use App\Notifications\Finance\TranstrakParseNotification;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\ServerException;
use GuzzleHttp\RequestOptions;
use Hostville\Dorcas\LaravelCompat\Auth\DorcasUser;
use Hostville\Dorcas\Sdk;
use Illuminate\Auth\GenericUser;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;

class TranstrakParseListener implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * @param TranstrakParse $event
     *
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Exception
     */
    public function handle(TranstrakParse $event)
    {
        $client = new Client([
            'base_uri' => Transtrak::TRANSTRAK_BASE,
            RequestOptions::TIMEOUT => 120.0,
            RequestOptions::CONNECT_TIMEOUT => 60.0
        ]);
        # client base
        try {
            $response = $client->request('POST', '/process-inbox', [
                RequestOptions::HEADERS => [
                    'Authorization' => 'Bearer ' . config('dorcas.transtrak_token')
                ],
                RequestOptions::FORM_PARAMS => $event->json
            ]);
            # send the request
            $json = json_decode((string) $response->getBody(), true);
        } catch (ClientException $e) {
            $json = json_decode((string) $e->getResponse()->getBody(), true);
        } catch (ServerException $e) {
            $json = json_decode((string) $e->getResponse()->getBody(), true);
        } catch (\Throwable $e) {
            $json = ['status' => 'error', 'reason' => $e->getMessage()];
        }
        $this->notifyUser($event, $json);
        # notify the user
        if (!empty($json['status']) && $json['status'] === 'error') {
            Log::error('TranstrakParseListener::handle() :: Failure: ', ['json' => $json]);
            throw new \Exception('Transtrak encountered issues processing your inbox: ' . $json['reason']);
        } else {
            unset($json['transactions']);
            Log::info('TranstrakParseListener::handle() :: Success!!!', ['json' => $json]);
        }
        return;
    }
    
    /**
     * @param TranstrakParse $event
     * @param array          $transtrakJson
     */
    protected function notifyUser(TranstrakParse $event, array $transtrakJson)
    {
        $sdk = app(Sdk::class);
        $sdk->setAuthorizationToken($event->authToken);
        # instantiate the sdk
        $query = $sdk->createProfileService()->send('get');
        if (!$query->isSuccessful()) {
            # we couldn't load the profile
            return;
        }
        $user = User::where('email', $query->getData()['email'])->first();
        if (empty($user)) {
            return;
        }
        # create the user account
        Notification::send($user, new TranstrakParseNotification($transtrakJson));
        # send the notification
    }
}
