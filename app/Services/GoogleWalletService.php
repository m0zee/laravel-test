<?php

namespace App\Services;

use Chiiya\LaravelPasses\Google\GoogleClient;
use Chiiya\Passes\Google\Components\Common\Barcode;
use Chiiya\Passes\Google\Components\Common\Image;
use Chiiya\Passes\Google\Components\Common\ImageModuleData;
use Chiiya\Passes\Google\Components\Common\LinksModuleData;
use Chiiya\Passes\Google\Components\Common\LocalizedString;
use Chiiya\Passes\Google\Components\Common\TextModuleData;
use Chiiya\Passes\Google\Components\Common\TimeInterval;
use Chiiya\Passes\Google\Components\Common\Uri;
use Chiiya\Passes\Google\Components\Common\DateTime;
use Chiiya\Passes\Google\Enumerators\BarcodeRenderEncoding;
use Chiiya\Passes\Google\Enumerators\BarcodeType;
use Chiiya\Passes\Google\Enumerators\MultipleDevicesAndHoldersAllowedStatus;
use Chiiya\Passes\Google\Enumerators\State;
use Chiiya\Passes\Google\Passes\GenericClass;
use Chiiya\Passes\Google\Passes\GenericObject;
use Chiiya\Passes\Google\Repositories\GenericClassRepository;
use Chiiya\Passes\Google\ServiceCredentials;
use Chiiya\Passes\Google\JWT;
use Illuminate\Support\Str;

class GoogleWalletService
{
    protected GenericClassRepository $classRepository;
    protected ServiceCredentials $credentials;

    public function __construct(
        protected GoogleClient $client,
    ) {
        $this->credentials = ServiceCredentials::parse(config('passes.google.credentials'));
        $this->classRepository = new GenericClassRepository($this->client);
    }

    /**
     * Get the class ID for generic passes
     */
    protected function getClassId(): string
    {
        $issuerId = config('passes.google.issuer_id');
        return "{$issuerId}.generic-pass-class";
    }

    /**
     * Create or update the generic pass class
     * This should be called once to set up the pass template
     */
    public function createPassClass(): void
    {
        $class = new GenericClass(
            id: $this->getClassId(),
            multipleDevicesAndHoldersAllowedStatus: MultipleDevicesAndHoldersAllowedStatus::MULTIPLE_HOLDERS
        );

        // Create or update the class
        try {
            $this->classRepository->create($class);
        } catch (\Exception $e) {
            // If class already exists, update it
            if (str_contains($e->getMessage(), 'already exists')) {
                $this->classRepository->update($class);
            } else {
                throw $e;
            }
        }
    }

    /**
     * Generate a pass object and return the signed JWT
     *
     * @param array $data Custom data for the pass
     * @return string JWT token for Add to Google Wallet button
     */
    public function generatePass(array $data = []): string
    {
        $issuerId = config('passes.google.issuer_id');
        $objectId = "{$issuerId}." . Str::uuid()->toString();

        // Create the pass object
        $object = new GenericObject(
            classId: $this->getClassId(),
            id: $objectId,
            cardTitle: LocalizedString::make('en', $data['card_title'] ?? 'My Pass'),
            header: LocalizedString::make('en', $data['header'] ?? 'Pass Header'),
            subheader: LocalizedString::make('en', $data['subheader'] ?? 'Pass Subheader'),
            logo: isset($data['logo']) && filter_var($data['logo'], FILTER_VALIDATE_URL) ? Image::make($data['logo']) : null,
            hexBackgroundColor: $data['background_color'] ?? '#4285f4',
            heroImage: isset($data['hero_image']) && filter_var($data['hero_image'], FILTER_VALIDATE_URL) ? Image::make($data['hero_image']) : null,
            state: State::ACTIVE,
            barcode: isset($data['barcode_value']) ? new Barcode(
                type: BarcodeType::QR_CODE,
                value: $data['barcode_value'],
                renderEncoding: BarcodeRenderEncoding::UTF_8,
            ) : null,
            validTimeInterval: isset($data['valid_from']) && isset($data['valid_until']) ? new TimeInterval(
                start: new DateTime(date: new \DateTime($data['valid_from'])),
                end: new DateTime(date: new \DateTime($data['valid_until']))
            ) : null,
            textModulesData: $this->buildTextModules($data['text_modules'] ?? []),
        );

        // Generate and sign JWT
        $jwt = new JWT(
            iss: $this->credentials->client_email,
            key: $this->credentials->private_key,
            origins: config('passes.google.origins'),
        );

        $jwt->addGenericObject($object);

        return $jwt->sign();
    }

    /**
     * Build text modules from array data
     */
    protected function buildTextModules(array $modules): array
    {
        $textModules = [];

        foreach ($modules as $key => $module) {
            $textModules[] = new TextModuleData(
                header: $module['header'] ?? "Label {$key}",
                body: $module['body'] ?? "Value {$key}",
                id: $module['id'] ?? "field-{$key}",
            );
        }

        // If no modules provided, add default ones
        if (empty($textModules)) {
            $textModules = [
                new TextModuleData(
                    header: 'Customer Name',
                    body: 'John Doe',
                    id: 'customer-name',
                ),
                new TextModuleData(
                    header: 'Valid Until',
                    body: now()->addMonth()->format('F d, Y'),
                    id: 'valid-until',
                )
            ];
        }

        return $textModules;
    }

    /**
     * Check if the pass class exists
     */
    public function passClassExists(): bool
    {
        try {
            $this->classRepository->get($this->getClassId());
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }
}

