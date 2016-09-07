<?php

require_once dirname(__FILE__).'/../modules/aws-autoloader.php';
use Aws\DynamoDb\DynamoDbClient;
use Aws\DynamoDb\Enum\AttributeAction;
use Aws\DynamoDb\Enum\ReturnValue;
use Aws\DynamoDb\Enum\Type;
use Aws\S3\S3Client;
use Aws\DynamoDb\Enum\ComparisonOperator;

require_once dirname(__FILE__).'/conf.php';

// aws_s3操作类(单例)
Class CAwsS3
{
    private static $_instance;
	private $AwsSecret;
	private $AwsRegion;
	private $S3Client;
	private $S3PhotoBucket;
	private $Project;


    private function __construct()
    {

    }

    private function __clone()  
    { 

    }

    public static function GetInstance()    
    {    
        if(!(self::$_instance instanceof self))   
        {    
            self::$_instance = new self();    
        }  
        return self::$_instance;    
    }   

    public function InitAwsS3($AwsKey, $AwsSecret, $AwsRegion, $S3PhotoBucket, $Project)
    {
    	$this->AwsKey = $AwsKey;
    	$this->AwsSecret = $AwsSecret;
    	$this->AwsRegion = $AwsRegion;
		$this->S3PhotoBucket = $S3PhotoBucket;
    	$this->Project = $Project;
        $this->S3Client = S3Client::factory(array(
                'key'    => $this->AwsKey,
                'secret' => $this->AwsSecret,
                'region' => $this->AwsRegion
            ));

    }

 	public function UploadFileToS3($srcfile, $s3desfile)
 	{
		$result = $this->S3Client->putObject(array(
	            'ACL'        => 'public-read',
	            'Bucket'     => $this->S3PhotoBucket,
	            'Key'        => $s3desfile,
	            'SourceFile' => $srcfile
	        ));
        // We can poll the object until it is accessible
        $this->S3Client->waitUntilObjectExists(array(
	            'Bucket'     => $this->S3PhotoBucket,
	            'Key'        => $s3desfile
	        ));
 	}


}


// aws_db操作类(单例)
Class CAwsDb
{
	private static $_instance;
	private $AwsKey;
	private $AwsSecret;
	private $AwsRegion;
	private $DbClient;
	private $Project;

    private function __construct()
    {

    }

    private function __clone()  
    { 

    }

    public static function GetInstance()    
    {    
        if(!(self::$_instance instanceof self))   
        {    
            self::$_instance = new self();    
        }  
        return self::$_instance;    
    }   


    public function InitAwsDb($AwsKey, $AwsSecret, $AwsRegion, $Project)
    {
    	$this->AwsKey = $AwsKey;
    	$this->AwsSecret = $AwsSecret;
    	$this->AwsRegion = $AwsRegion;
    	$this->Project = $Project;
        $this->DbClient = DynamoDbClient::factory(array(
                'key'    => $this->AwsKey,
                'secret' => $this->AwsSecret,
                'region' => $this->AwsRegion
            ));  	

    }

    public function GetDbClient()
    {
        return $this->DbClient;
    }

}

?>
