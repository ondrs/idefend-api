<?php


namespace ondrs\iDefendApi;

class PolicyDoc
{
    const TYPE_ENDORSEMENT = 'Endorsement';
    const TYPE_INVOICE = 'Invoice';
    const TYPE_OTHER = 'Other';
    const TYPE_CORRESPONDENCE = 'Correspondence';
    const TYPE_CLAIM_AUTHORIZATION = 'Claim Authorization';
    const TYPE_ERROR_AMENDMENT = 'Error Amendment';
    const TYPE_POWER_OF_ATTORNEY = 'Power of Attorney';
    const TYPE_PREMIUM_REFUND = 'Premium Refund';
    const TYPE_APPLICATION = 'Application';
    const TYPE_TECHNICAL_DOCS = 'Technical Docs';
    const TYPE_VOUCHER_1 = 'Voucher 1';

    const DEFAULT_TYPE = self::TYPE_OTHER;

    /** @var \SplFileInfo */
    private $fileInfo;

    /** @var string */
    private $filename;

    /** @var string */
    private $type = self::DEFAULT_TYPE;

    /** @var string */
    private $info;


    /**
     * @param string $file
     * @param string $type
     * @param NULL|string $info
     * @throws iDefendFileNotFoundException
     */
    public function __construct($file, $type = self::DEFAULT_TYPE, $info = NULL)
    {
        if (!file_exists($file)) {
            throw new iDefendFileNotFoundException("File $file does not exists");
        }

        $this->fileInfo = new \SplFileInfo($file);

        $this->filename = $this->fileInfo->getFilename();
        $this->type = $type;
        $this->info = $info;
    }


    /**
     * @return object
     * @throws iDefendIOException
     */
    public function getValues()
    {
        $contents = @file_get_contents($this->fileInfo);

        if (!$contents) {
            throw new iDefendIOException("There was a problem while reading a content of the file $this->fileInfo");
        }

        return (object)[
            'filename' => $this->filename,
            'contents' => base64_encode($contents),
            'type' => $this->type,
            'info' => $this->info,
        ];
    }

}
