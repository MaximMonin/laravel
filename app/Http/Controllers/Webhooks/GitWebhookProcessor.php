<?php

namespace App\Http\Controllers\Webhooks;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class GitWebhookProcessor
{

    /**
     * Validate an incoming github webhook
     *
     * @param string $known_token Our known token that we've defined
     * @param \Illuminate\Http\Request $request
     *
     * @throws \BadRequestHttpException, \UnauthorizedException
     * @return void
     */
    protected function validateGithubWebhook($known_token, Request $request)
    {
        if (($signature = $request->headers->get('X-Hub-Signature')) == null) {
            throw new BadRequestHttpException('Header not set');
        }

        $signature_parts = explode('=', $signature);

        if (count($signature_parts) != 2) {
            throw new BadRequestHttpException('signature has invalid format');
        }

        $known_signature = hash_hmac('sha1', $request->getContent(), $known_token);

        if (! hash_equals($known_signature, $signature_parts[1])) {
            throw new UnauthorizedException('Could not verify request signature ' . $signature_parts[1]);
        }
    }

    protected function validateGitlabWebhook($known_token, Request $request)
    {
        if (($signature = $request->headers->get('X-Gitlab-Token')) == null) {
            throw new BadRequestHttpException('Header not set');
        }

        if ($known_token !== $signature) {
            throw new UnauthorizedException('Could not verify request signature ' . $signature);
        }
    }



    /**
     * Entry point to our webhook handler
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return mixed
     */
    public function github(Request $request)
    {
        $this->validateGithubWebhook(config('app.github_webhook_secret'), $request);

        Log::info('github webhooks: ' . $request->getContent());
    }

    /**
     * Entry point to our webhook handler
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return mixed
     */
    public function gitlab(Request $request)
    {
        $this->validateGitlabWebhook(config('app.gitlab_webhook_secret'), $request);

        Log::info('gitlab webhooks: ' . $request->getContent());
    }
}
