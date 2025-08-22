<?php

namespace App\Services;

use net\authorize\api\contract\v1 as AnetAPI;
use net\authorize\api\controller as AnetController;
use Illuminate\Support\Facades\Config;

class AuthorizeNetService
{
    private string $loginId;
    private string $transactionKey;
    private string $environment;
    private ?string $clientKey = null;

    public function __construct()
    {
        $this->loginId = Config::get('services.authorizenet.api_login_id');
        $this->transactionKey = Config::get('services.authorizenet.transaction_key');
        $this->environment = Config::get('services.authorizenet.environment', 'sandbox');
        $this->clientKey = Config::get('services.authorizenet.client_key');
    }

    private function merchantAuthentication(): AnetAPI\MerchantAuthenticationType
    {
        $merchantAuthentication = new AnetAPI\MerchantAuthenticationType();
        $merchantAuthentication->setName($this->loginId);
        $merchantAuthentication->setTransactionKey($this->transactionKey);
        return $merchantAuthentication;
    }

    private function getEndpoint(): string
    {
        return $this->environment === 'production'
            ? \net\authorize\api\constants\ANetEnvironment::PRODUCTION
            : \net\authorize\api\constants\ANetEnvironment::SANDBOX;
    }

    public function ensureCustomerProfile(string $email, string $description, ?string $paymentNonce = null): array
    {
        $customerProfile = new AnetAPI\CustomerProfileType();
        $customerProfile->setEmail($email);
        $customerProfile->setDescription($description);

        $request = new AnetAPI\CreateCustomerProfileRequest();
        $request->setMerchantAuthentication($this->merchantAuthentication());
        $request->setProfile($customerProfile);
        $request->setValidationMode('none');

        $controller = new AnetController\CreateCustomerProfileController($request);
        $response = $controller->executeWithApiResponse($this->getEndpoint());

        if ($response && $response->getMessages()->getResultCode() === 'Ok') {
            return ['customerProfileId' => $response->getCustomerProfileId()];
        }

        $message = $response?->getMessages()?->getMessage()[0] ?? null;
        throw new \RuntimeException('Authorize.Net create profile failed: '.($message?->getText() ?? 'Unknown error'));
    }

    public function addPaymentProfile(string $customerProfileId, string $cardNumber, string $expiration, string $cvv, string $firstName, string $lastName): array
    {
        $creditCard = new AnetAPI\CreditCardType();
        $creditCard->setCardNumber($cardNumber);
        $creditCard->setExpirationDate($expiration); // YYYY-MM format
        $creditCard->setCardCode($cvv);

        $paymentType = new AnetAPI\PaymentType();
        $paymentType->setCreditCard($creditCard);

        $billTo = new AnetAPI\CustomerAddressType();
        $billTo->setFirstName($firstName);
        $billTo->setLastName($lastName);

        $paymentProfile = new AnetAPI\CustomerPaymentProfileType();
        $paymentProfile->setCustomerType('individual');
        $paymentProfile->setPayment($paymentType);
        $paymentProfile->setBillTo($billTo);

        $request = new AnetAPI\CreateCustomerPaymentProfileRequest();
        $request->setMerchantAuthentication($this->merchantAuthentication());
        $request->setCustomerProfileId($customerProfileId);
        $request->setPaymentProfile($paymentProfile);
        $request->setValidationMode('none');

        $controller = new AnetController\CreateCustomerPaymentProfileController($request);
        $response = $controller->executeWithApiResponse($this->getEndpoint());

        if ($response && $response->getMessages()->getResultCode() === 'Ok') {
            return ['customerPaymentProfileId' => $response->getCustomerPaymentProfileId()];
        }

        $message = $response?->getMessages()?->getMessage()[0] ?? null;
        throw new \RuntimeException('Authorize.Net add payment profile failed: '.($message?->getText() ?? 'Unknown error'));
    }

    // Accept.js opaque data payment profile
    public function addPaymentProfileOpaque(string $customerProfileId, string $opaqueDataDescriptor, string $opaqueDataValue, string $firstName, string $lastName): array
    {
        $opaqueData = new AnetAPI\OpaqueDataType();
        $opaqueData->setDataDescriptor($opaqueDataDescriptor); // e.g., COMMON.ACCEPT.INAPP.PAYMENT
        $opaqueData->setDataValue($opaqueDataValue);

        $paymentType = new AnetAPI\PaymentType();
        $paymentType->setOpaqueData($opaqueData);

        $billTo = new AnetAPI\CustomerAddressType();
        $billTo->setFirstName($firstName);
        $billTo->setLastName($lastName);

        $paymentProfile = new AnetAPI\CustomerPaymentProfileType();
        $paymentProfile->setPayment($paymentType);
        $paymentProfile->setBillTo($billTo);

        $request = new AnetAPI\CreateCustomerPaymentProfileRequest();
        $request->setMerchantAuthentication($this->merchantAuthentication());
        $request->setCustomerProfileId($customerProfileId);
        $request->setPaymentProfile($paymentProfile);
        $request->setValidationMode('none');

        $controller = new AnetController\CreateCustomerPaymentProfileController($request);
        $response = $controller->executeWithApiResponse($this->getEndpoint());

        if ($response && $response->getMessages()->getResultCode() === 'Ok') {
            return ['customerPaymentProfileId' => $response->getCustomerPaymentProfileId()];
        }
        $message = $response?->getMessages()?->getMessage()[0] ?? null;
        throw new \RuntimeException('Authorize.Net add payment profile (opaque) failed: '.($message?->getText() ?? 'Unknown error'));
    }

    public function createARBSubscription(string $customerProfileId, string $paymentProfileId, string $planName, int $amountCents, string $intervalUnit = 'months', int $intervalLength = 1): array
    {
        $arbSubscription = new AnetAPI\ARBSubscriptionType();
        $arbSubscription->setName($planName);

        $interval = new AnetAPI\PaymentScheduleType\IntervalAType();
        $interval->setLength($intervalLength);
        $interval->setUnit($intervalUnit === 'days' ? 'days' : 'months');

        $paymentSchedule = new AnetAPI\PaymentScheduleType();
        $paymentSchedule->setInterval($interval);
        $paymentSchedule->setStartDate(new \DateTime());
        $paymentSchedule->setTotalOccurrences(9999);

        $amount = number_format($amountCents / 100, 2, '.', '');

        $profile = new AnetAPI\CustomerProfileIdType();
        $profile->setCustomerProfileId($customerProfileId);
        $profile->setCustomerPaymentProfileId($paymentProfileId);

        $arbSubscription->setPaymentSchedule($paymentSchedule);
        $arbSubscription->setAmount($amount);
        $arbSubscription->setProfile($profile);

        $request = new AnetAPI\ARBCreateSubscriptionRequest();
        $request->setMerchantAuthentication($this->merchantAuthentication());
        $request->setSubscription($arbSubscription);

        $controller = new AnetController\ARBCreateSubscriptionController($request);
        $response = $controller->executeWithApiResponse($this->getEndpoint());

        if ($response && $response->getMessages()->getResultCode() === 'Ok') {
            return ['subscriptionId' => $response->getSubscriptionId()];
        }

        $message = $response?->getMessages()?->getMessage()[0] ?? null;
        throw new \RuntimeException('Authorize.Net create subscription failed: '.($message?->getText() ?? 'Unknown error'));
    }

    public function getARBStatus(string $subscriptionId): string
    {
        $request = new AnetAPI\ARBGetSubscriptionStatusRequest();
        $request->setMerchantAuthentication($this->merchantAuthentication());
        $request->setSubscriptionId($subscriptionId);

        $controller = new AnetController\ARBGetSubscriptionStatusController($request);
        $response = $controller->executeWithApiResponse($this->getEndpoint());

        if ($response && $response->getMessages()->getResultCode() === 'Ok') {
            return $response->getStatus();
        }

        $message = $response?->getMessages()?->getMessage()[0] ?? null;
        throw new \RuntimeException('Authorize.Net get subscription status failed: '.($message?->getText() ?? 'Unknown error'));
    }

    public function cancelARB(string $subscriptionId): bool
    {
        $request = new AnetAPI\ARBCancelSubscriptionRequest();
        $request->setMerchantAuthentication($this->merchantAuthentication());
        $request->setSubscriptionId($subscriptionId);

        $controller = new AnetController\ARBCancelSubscriptionController($request);
        $response = $controller->executeWithApiResponse($this->getEndpoint());

        return $response && $response->getMessages()->getResultCode() === 'Ok';
    }
}


