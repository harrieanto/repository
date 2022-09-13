<?php
namespace Repository\Component\Mail;

use Repository\Component\Filesystem\Filesystem as Fs;
use Repository\Component\Contracts\Container\ContainerInterface;

/**
 * Email Parameter.
 *
 * @package	  \Repository\Component\Mail
 * @author    Hariyanto - harrieanto31@yahoo.com
 * @version   1.0
 * @link      https://www.bandd.web.id
 * @copyright Copyright (C) 2019 Hariyanto
 * @license   https://github.com/harrieanto/repository/blob/master/LICENSE.md
 */
class Mailer
{
	/** The carriage return and newline **/
	const RN = "\r\n";

	/**
	 * The mail recipient
	 * @var array
	 */
	protected $_to 	= array();

	/**
	 * The mail sender
	 * @var string
	 */
	protected $_from;

	/**
	 * The mail subject
	 * @var string $subject
	 */
	protected $subject;

	/**
	 * The mail message
	 * @var string $message
	 */
	protected $message;

	/**
	 * Set generic headers
	 * @var array $headers
	 */
	protected $headers = array();

	/**
	 * Attachment headers stream
	 * @var array $assembleAttachmentHeaders
	 */
	protected $assembleAttachmentHeaders = array();

	/**
	 * Attachment body stream
	 * @var array $assembleAttachmentBody
	 */
	protected $assembleAttachmentBody = array();

	/**
	 * Attachments
	 * @var array $attachments
	 */
	protected $attachments = array();

	/**
	 * Unique id
	 * @var string
	 */
	protected $uid;

	/**
	 * The number of word wrap
	 * @var integer $wrap
	 */
	protected $wrap = 78;

	/**
	 * Container instance
	 * @var \Repository\Component\Contracts\Container\ContainerInterface $app
	 */
	protected $app;

	/**
	 * Charset instance
	 * @var \Repository\Component\Mail\CharsetConverter $charset
	 */	
	protected $charset;
	
	public function __construct(ContainerInterface $app)
	{
		$this->app = $app;
		$this->charset = new CharsetConverter();
		$this->initialize();
	}
	
	public function getMailParameter($key)
	{
		$param = $this->app['config']['mailer'][$key];
		
		return $param;
	}

	/**
	 * Set recipients
	 * 
	 * @param string $email Email recipient
	 * @param string $name  Name recipient
	 * 
	 * @return \Repository\Component\Mail\MailParameter
	 */
	public function setRecipient(string $email, string $name)
	{
		$this->_to[] = $this->formatAddressHeader($email, $name);

		return $this;
	}

	/**
	 * Get email recipients
	 * 
	 * @return array
	 */
	public function getRecipients()
	{
		return $this->_to;
	}

	/**
	 * Set email subject
	 * 
	 * @param string $subject Email subject
	 * 
	 * @return \Repository\Component\Mail\MailParameter
	 */
	public function setSubject(string $subject)
	{
		$this->subject = $this->charset->encodeUtf8(
			$this->filter($subject)
		);

		return $this;
	}

	/**
	 * Get email subjects
	 * 
	 * @return string
	 */
	public function getSubject()
	{
		return $this->subject;
	}

	/**
	 * Set email attachments
	 * 
	 * @param string $path File pathname
	 * @param string $filename
	 * 
	 * @return \Repository\Component\Mail\MailParameter
	 */
	public function setAttachment($path, $filename = null)
	{
		$filename = (empty($filename))? basename($path):$filename;
		$target = $path.Fs::DS.$filename;

		$this->attachments[] = array(
			'path'	=> $path,
			'file'	=> $filename,
			'data'	=> $this->getAttachment($target)
			);

		return $this;
	}

	/**
	 * Get attachment and encode attachment
	 * 
	 * @param  string $attachment File attachment
	 * 
	 * @return Encoded attachment
	 */
	public function getAttachment(string $attachment)
	{
		$filesize 	= filesize($attachment);
		$handle 	= fopen($attachment, 'r');
		$attachment = fread($handle, $filesize);
		fclose($handle);

		return chunk_split(base64_encode($attachment));
	}

	/**
	 * Add from header to the email header
	 * 
	 * @param string $email The email address of sender
	 * @param string $name  The name of email sender
	 *
	 * @return \Repository\Component\Mail\MailParameter
	 */
	public function setFrom(string $email, string $name)
	{
		$this->_from = $email;
		$address = $this->formatAddressHeader($email, $name);
		$this->headers['from'] = sprintf("%s: %s", 'From', $address);

		return $this;
	}

	/**
	 * Set email header
	 * 
	 * @param string $header The header type
	 * @param string $email  email adddress
	 * @param string $name   email name
	 *
	 * @return \Repository\Component\Mail\MailParameter
	 */
	public function setHeader(string $header, string $email, string $name)
	{
		$address = $this->formatAddressHeader($email, $name);
		$this->headers[$header] = sprintf("%s: %s", $header, $address);

		return $this;
	}

	/**
	 * Get initialized headers
	 * 
	 * @return array
	 */
	public function getHeaders()
	{
		return (array) $this->headers;
	}

	/**
	 * Set additional generic header
	 * 
	 * @param string $header header type name
	 * @param string $value  value of type header
	 * 
	 * @return \Repository\Component\Mail\MailParameter
	 * 
	 */
	public function setGenericHeader(string $header, string $value)
	{
		$this->headers[$header] = sprintf("%s: %s", $header, $value);

		return $this;
	}

	/**
	 * Determine if the initialized email has an attachments
	 * 
	 * @return boolean
	 */
	public function hasAttachments()
	{
		return (bool) !empty($this->attachments);
	}

	/**
	 * Get assemble attachments header
	 * 
	 * @return string Assemble headers
	 */
	public function assembleAttachmentHeaders()
	{
		$this->assembleAttachmentHeaders[] = "MIME-Version: 1.0";
		$this->assembleAttachmentHeaders[] = "Content-Type: multipart/mixed; boundary=\"{$this->uid}\"";

		return implode(PHP_EOL, $this->assembleAttachmentHeaders);
	}

	/**
	 * Get assemble attachments body
	 * 
	 * @return string Attachment body
	 */
	public function assembleAttachmentBody()
	{
		$uid = $this->uid;

		$this->assembleAttachmentBody[] = "This is multi-part message in MIME format";
		$this->assembleAttachmentBody[] = "--{$this->uid}";
		$this->assembleAttachmentBody[] = "Content-Type: text/html; charset=\"utf-8\"";
		$this->assembleAttachmentBody[] = "Content-Transfer-Encoding: 7-bit";
		$this->assembleAttachmentBody[] = "";
		$this->assembleAttachmentBody[] = $this->message;
		$this->assembleAttachmentBody[] = "";
		$this->assembleAttachmentBody[] = "--{$uid}";

		//unboxing attachments
		array_map(function($attachment) {
			$this->assembleAttachmentBody[] = $this->assembleAttachment($attachment);
		}, $this->attachments);

		return implode(PHP_EOL, $this->assembleAttachmentBody);
	}

	/**
	 * 
	 * grab all file attachments and mime template 
	 * 
	 * @param  array $attachment attachment
	 * 
	 * @return string
	 */
	public function assembleAttachment($attachments)
	{
		$mimes = array();
		$uid = $this->uid;

		$attachment = $attachments['file'];
		$contentAttachment = $attachments['data'];

		$mimes[] = "Content-Type: application/octet-stream; name=\"$attachment\"";
		$mimes[] = "Content-Transfer-Encoding: base64";
		$mimes[] = "Content-Disposition: attachment; filename=\"$attachment\"";
		$mimes[] = "";
		$mimes[] = $contentAttachment;
		$mimes[] = "";
		$mimes[] = "--{$uid}";

		return implode(PHP_EOL, $mimes);
	}

	/**
	 * Set email message and resolve issues of windows behaviours
	 *
	 * @param string $message The message to send.
	 *
	 * @return \Repository\Component\Mail\MailParameter
	 */
	public function setMessage(string $message)
	{
		$this->message = str_replace("\n.", "\n..", $message);

		return $this;
	}

	/**
	 * 
	 * Get iinitialized email message
	 *
	 * @return string
	 */
	public function getMessage()
	{
		return (string) $this->message;
	}

	/**
	 * Formats a display address for emails according to RFC2822 e.g.
	 * Name <address@domain.tld>
	 *
	 * @param string $email The email address.
	 * @param string $name  The display name.
	 *
	 * @return string
	 */
	public function formatAddressHeader(string $email, $name = null)
	{
		$email = $this->filterEmailAddress($email);
		
		if (empty($name)) return $email;
		
		$name = $this->charset->encodeUtf8($this->filterName($name));

		return sprintf('%s <%s>', $name, $email);
	}

	/**
	 * Removes any carriage return, line feed, tab, double quote, comma
	 * and angle bracket characters before sanitizing the email address.
	 *
	 * @param string $email The email to filter.
	 *
	 * @return string
	 */
	public function filterEmailAddress($email)
	{
		$rule = array(
			"\r" => '',
			"\n" => '',
			"\t" => '',
			'"'  => '',
			','  => '',
			'<'  => '',
			'>'  => ''
		);

		$email = strtr($email, $rule);
		$email = filter_var($email, FILTER_SANITIZE_EMAIL);

		return $email;
	}

	/**
	 * Removes any carriage return, line feed or tab characters. Replaces
	 * double quotes with single quotes and angle brackets with square
	 * brackets, before sanitizing the string and stripping out html tags.
	 *
	 * @param string $name The name to filter.
	 *
	 * @return string
	 */
	public function filterName($name)
	{
		$rule = array(
			"\r" => '',
			"\n" => '',
			"\t" => '',
			'"'  => "'",
			'<'  => '[',
			'>'  => ']',
	   );

		$filtered = filter_var(
			$name,
			FILTER_SANITIZE_STRING,
			FILTER_FLAG_NO_ENCODE_QUOTES
	   );

		return trim(strtr($filtered, $rule));
	}

	/**
	 * Removes ASCII control characters including any carriage return, line
	 * feed or tab characters.
	 *
	 * @param string $data The data to filter.
	 *
	 * @return string
	 */
	public function filter($data)
	{
		return filter_var($data, FILTER_UNSAFE_RAW, FILTER_FLAG_STRIP_LOW);
	}

	/**
	 * Set the word wrap length
	 *
	 * @param int $wrap The number of characters at which the message will wrap.
	 *
	 * @return \Repository\Component\Mail\MailParameter
	 */
	public function setWrap($wrap = 78)
	{
		$wrap = (int) $wrap;

		if ($wrap < 1) $wrap = 78;

		$this->wrap = $wrap;

		return $this;
	}

	/**
	 * Get the lenght of word wrap
	 *
	 * @return int
	 */
	public function getWrap()
	{
		return (int) $this->wrap;
	}

	/**
	 * Get initialized email headers as row stream
	 * 
	 * @return string
	 */
	public function getHeadersAsRow()
	{
		$header = (empty($this->headers))? "" : implode("\r\n", $this->headers);
		
		return $header;
	}

	/** 
	 * Get email recipient
	 * 
	 * @return string
	 */
	public function getRecipient()
	{
		return (empty($this->_to))? "" : implode(", ", $this->_to);
	}

	/**
	 * Wrap email message
	 *
	 * @return string
	 */
	public function getWrapMessage()
	{
		return wordwrap($this->message, $this->wrap);
	}

	/**
	 * Get resolved headers as row string
	 *
	 * @return string
	 */
	public function getResolvedHeader()
	{
		$headers = $this->getHeadersAsRow();

		if ($this->hasAttachments())
			$headers.= PHP_EOL . $this->assembleAttachmentHeaders();

		return $headers;
	}

	/**
	 * Get resolved email body as row string
	 *
	 * @return string
	 */
	public function getResolvedBody()
	{
		$message = '';

		if ($this->hasAttachments()) {
			$message = $this->assembleAttachmentBody();
		}else{
			$message = $this->getWrapMessage();
		}

		return $message;
	}

	/**
	 * Generate unique id
	 *
	 * @return string
	 */
	public function getUniqueId()
	{
		return md5(uniqid(time()));
	}

	/**
	 * Get email sender
	 *
	 * @return string
	 */
	public function getSender()
	{
		return (string) $this->_from;
	}
	
	/**
	 * Resets all properties to initial state.
	 *
	 * @return void
	 */
	public function initialize()
	{
		$this->__destruct();
	}

	/**
	 * Convert the object to the string
	 * 
	 * @return string
	 */
	public function __toString()
	{
		return print_r($this, true);
	}

	/**
	 * Resets all properties to initial state.
	 *
	 * @return void
	 */
	public function __destruct()
	{
		$this->_to 		= [];
		$this->headers 	= [];
		$this->subject 	= null;
		$this->message 	= null;
		$this->wrap 	= 78;
		$this->attachments = [];
		$this->uid 		= $this->getUniqueId();
	}
}