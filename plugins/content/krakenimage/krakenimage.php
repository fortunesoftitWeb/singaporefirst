<?php


// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );
require_once("kraken-php-api/lib/Kraken.php");
 
class PlgContentKrakenImage extends JPlugin
{
    var $version = "1.0.1";

    protected $autoloadLanguage = true;
    protected $componentParams = null;
    protected $componentInstalled = false;
    protected $componentEnabled = false;
    
   	/**
	 * Constructor
	 *
	 * @access      protected
	 * @param       object  $subject The object to observe
	 * @param       array   $config  An array that holds the plugin configuration
	 * @since       1.0
	 */
	public function __construct(& $subject, $config)
	{
		parent::__construct($subject, $config);
                
        // Check if kraken Image Component is installed and enabled
        $is_enabled = krakenImageHelper::iskrakenImageComponentEnabled( );
        if ( $is_enabled != null )
        {
            $this->componentInstalled = true;
            $this->componentEnabled = $is_enabled;
        }
           
        // Get the parameters from the kraken Image Component        
        if ( $this->componentInstalled )
            $this->componentParams = JComponentHelper::getParams( 'com_krakenimage' );

        /**
        * Constants
        */
        define( 'IMAGE_SERVICE_DATABASE', '#__krakenimage' );
        
        if ( $this->componentParams )
        {
            define( 'IMAGE_SERVICE_DEBUG', $this->componentParams->get('pi_debug') ); 
            define( 'IMAGE_SERVICE_AUTO', $this->componentParams->get('pi_processOnUpload') );
        }
        else
        {
            define( 'IMAGE_SERVICE_DEBUG', false ); 
            define( 'IMAGE_SERVICE_AUTO', false );               
        }
       
        // Load language file
        JFactory::getLanguage()->load('plg_content_krakenimage');
        
	}

    public function onContentBeforeSave($context, &$article, $isnew)
    {
        // bail out if not being called from the upload() in 'com_media'
        if ($context != 'com_media.file')
            return true;
            
        // Continue only if the kraken Image Component is installed
        if (!$this->componentInstalled)
            return true;
            
        // Continue only if automatic processing for uploads is enabled
        if (!IMAGE_SERVICE_AUTO)
            return true;
        
        // Call onkrakenImageReduce directly
        $this->onkrakenImageReduce($context, $article, $isnew, true,$id);
        
	    return true;
    }
    
    // Event that calls the kraken Image Service
    // Same parameters as an onContentBeforeSave
    // $article is really an $object_file object with the following fields:
    //     tmp_name - the path to the file or temporary file that will be reduced
    //     filepath - the final path of the file that will be reduced
    //     size - the size of the 'orginal' file in bytes
    // $displayMessage - Boolean to determine if final status message is displayed, overriddened by IMAGE_SERVICE_DEBUG
    public function onkrakenImageReduce($context, &$article, $isnew, $displayMessage,$id)
    {
		
		$kraken = new Kraken("c13ddc03d24406418e4bcf36457eaac0", "c78e4d3f561739aae6a02b3bc89b27c2b54911d0");

	    // bail out if not being called from the upload() in 'com_media' or from reduce() in 'com_krakenimage'
        if (!($context == 'com_media.file' || $context == 'com_krakenimage.file'))
            return true;

        // Get a handle to the Joomla! application object
        $app = JFactory::getApplication();       
        $object_file = $article;
        
		// Check to make sure this is a valid image type handled by the image service
        if (!krakenImageHelper::file_is_handled_by_image_service($object_file->filepath))
        {
            if ( IMAGE_SERVICE_DEBUG )
                $app->enqueueMessage(JText::sprintf('PLG_CONTENT_KRAKENIMAGE_INVALID_FILETYPE', JFile::getName($object_file->filepath)), 'Warning');
            return true;
        }
  
        // Use the tmp_name because that is the path from all calls to this event
        $tempfile_path = $object_file->tmp_name;
        //$file_path = $object_file->filepath;        
       // $headers = array('user-agent' => IMAGE_SERVICE_UA);
       // $file_contents = file_get_contents($tempfile_path);       
		
       // Invoke the POST request.
        $params = array(
        		"file" => $article->tmp_name,
        		"wait" => true,
		);
        
        $data = $kraken->upload($params,$id);
        
        if ($data["success"]) {
        	$optimized_image_url = $data["kraked_url"];
        } else {
        	$optimized_image_url = '';
        }
        
        $response = $data;
      	$success = true;
        $status = 0;
        $message = '';
        $newFilesize = $object_file->size;
        
       //print_r($response);
              
        if($response["success"])
        {
            $content = file_get_contents($response["kraked_url"]);
            file_put_contents($tempfile_path, $content);
            $newFilesize = $response["kraked_size"];
        }
        else
        {
            $success = false;
            $status = -1;
            $message = JText::sprintf('PLG_CONTENT_KRAKENIMAGE_SERVER_STATUS', $response["code"]);
        }
 
       // Get the file name only just for display
        $fileNameOnly = JFile::getName($object_file->filepath);
        
        // update the kraken Image database
		$filepath = str_replace(DIRECTORY_SEPARATOR, '/', $object_file->filepath);
        $result = $this->updateDatabase(krakenImageHelper::getRelativeMediaPath($filepath), $object_file->size, $newFilesize, $status);
        if (IMAGE_SERVICE_DEBUG)
        {
            if ($result == true)
                $app->enqueueMessage(JText::sprintf('PLG_CONTENT_KRAKENIMAGE_DATABASE_ADD_SUCCESS', $fileNameOnly), 'Message');
            else
                $app->enqueueMessage(JText::sprintf('PLG_CONTENT_KRAKENIMAGE_DATABASE_ADD_FAIL', $fileNameOnly), 'Warning');
        }

        // Display success or fail message
        if ($success)
        {
            if ($newFilesize != $object_file->size)
            {
                // Add to the total bytes saved
                $totalBytesSaved = $this->componentParams->get('pi_totalBytesSaved');
                $totalBytesSaved += ($object_file->size - $newFilesize);
                krakenImageHelper::setkrakenImageParam('pi_totalBytesSaved', $totalBytesSaved);
                
                if ($displayMessage)
                    $app->enqueueMessage(JText::sprintf('PLG_CONTENT_KRAKENIMAGE_REDUCE_SUCCESS', $fileNameOnly, $object_file->size, $newFilesize), 'Message');
            }
            else
                if ($displayMessage)
                    $app->enqueueMessage(JText::sprintf('PLG_CONTENT_KRAKENIMAGE_REDUCE_NOSAVINGS', $fileNameOnly), 'Message');                
        }
        else
        {
            $app->enqueueMessage(JText::sprintf('PLG_CONTENT_KRAKENIMAGE_REDUCE_FAIL', $fileNameOnly, $message ), 'Error');
        }
            
	    return true;
    }
       
    public function onkrakenImageUrl($context, &$article, $isnew, $displayMessage)
    {
    
    	$kraken = new Kraken("c13ddc03d24406418e4bcf36457eaac0", "c78e4d3f561739aae6a02b3bc89b27c2b54911d0");
    	
       	// bail out if not being called from the upload() in 'com_media' or from reduce() in 'com_krakenimage'
    	if (!($context == 'com_media.file' || $context == 'com_krakenimage.file'))
    		return true;
    
    	// Get a handle to the Joomla! application object
    	$app = JFactory::getApplication();
    	$object_file = $article;
    
       	// Check to make sure this is a valid image type handled by the image service
    	if (!krakenImageHelper::file_is_handled_by_image_service($object_file->tmp_name))
    	{
    		if ( IMAGE_SERVICE_DEBUG )
    			$app->enqueueMessage(JText::sprintf('PLG_CONTENT_KRAKENIMAGE_INVALID_FILETYPE', JFile::getName($object_file->filepath)), 'Warning');
    		return true;
    	}
    
    	// Use the tmp_name because that is the path from all calls to this event
    	$object_file->filepath = rtrim($object_file->filepath, "/");
    	$tempfile_path = $object_file->filepath;
    
    	// Invoke the POST request.
    	$params = array(
    				"url" => $article->tmp_name,
    				"wait" => true
    				//"lossy" => $this->componentParams->get('pi_optimization_type')
    		);
        		 
    	$data = $kraken->url($params);
    	
       	if ($data["success"]) {
    		$optimized_image_url =  $data["kraked_url"];
    		$object_file->size = $data["original_size"];
    		$object_file->filepath = $tempfile_path = $tempfile_path."/".$data["file_name"];
    	} else {
    		$optimized_image_url =  '';
    	}
    
    	$response = $data;
    
    	$success = true;
    	$status = 0;
    	$message = '';
    	$newFilesize = $object_file->size;
    
    	if ($response["success"])
    	{
    		$content = file_get_contents($response["kraked_url"]);
    		file_put_contents($tempfile_path, $content);
    		$newFilesize = $response["kraked_size"];
		}
    	else
    	{
    		$success = false;
    		$status = -1;
      		$message = JText::sprintf('PLG_CONTENT_KRAKENIMAGE_SERVER_STATUS', $response->code);
    	}
    
    	// Get the file name only just for display
    	$fileNameOnly = JFile::getName($object_file->filepath);
    
    	// update the kraken Image database
    	$filepath = str_replace(DIRECTORY_SEPARATOR, '/', $object_file->filepath);
    	$result = $this->updateDatabase(krakenImageHelper::getRelativeMediaPath($filepath), $object_file->size, $newFilesize, $status);
    	if (IMAGE_SERVICE_DEBUG)
    	{
    		if ($result == true)
    			$app->enqueueMessage(JText::sprintf('PLG_CONTENT_KRAKENIMAGE_DATABASE_ADD_SUCCESS', $fileNameOnly), 'Message');
    		else
    			$app->enqueueMessage(JText::sprintf('PLG_CONTENT_KRAKENIMAGE_DATABASE_ADD_FAIL', $fileNameOnly), 'Warning');
    	}
    
    	// Display success or fail message
    	if ($success)
    	{
    		if ($newFilesize != $object_file->size)
    		{
    			// Add to the total bytes saved
    			$totalBytesSaved = $this->componentParams->get('pi_totalBytesSaved');
    			$totalBytesSaved += ($object_file->size - $newFilesize);
    			krakenImageHelper::setkrakenImageParam('pi_totalBytesSaved', $totalBytesSaved);
       
				if ($displayMessage)
    				$app->enqueueMessage(JText::sprintf('PLG_CONTENT_KRAKENIMAGE_REDUCE_SUCCESS', $fileNameOnly, $object_file->size, $newFilesize), 'Message');
    		}
    		else
    		if ($displayMessage)
    			$app->enqueueMessage(JText::sprintf('PLG_CONTENT_KRAKENIMAGE_REDUCE_NOSAVINGS', $fileNameOnly), 'Message');
    	}
    	else
    	{
    		$app->enqueueMessage(JText::sprintf('PLG_CONTENT_KRAKENIMAGE_REDUCE_FAIL', $fileNameOnly, $message ), 'Error');
    	}
    
    	return true;
    }
    
    public function onContentAfterDelete($context, $article)
	{       
        // bail out if not being called from the delete() in 'com_media'
        if ($context != 'com_media.file')
            return true;

        // Get a handle to the Joomla! application object
        $app = JFactory::getApplication();       
        $object_file = $article;
                    
        // Display debugging enabled message when debug is on
        if (IMAGE_SERVICE_DEBUG)
        {
            $app->enqueueMessage(JText::_('PLG_CONTENT_KRAKENIMAGE_DEBUG_ENABLED'), 'Warning');
        }
            
        // Check to make sure this is a valid image type handled by the image service
        if (!krakenImageHelper::file_is_handled_by_image_service($object_file->filepath))
        {
            return true;
        }
        
        // Check to see if the file is in the krakenImage database
        $filepath = str_replace(DIRECTORY_SEPARATOR, '/', $object_file->filepath);
        $fileId = $this->getFileDatabaseId( krakenImageHelper::getRelativeMediaPath($object_file->filepath) );    
        if ($fileId != 0)
        {
            $result = $this->deleteFileFromDatabase($fileId);
            if ( IMAGE_SERVICE_DEBUG )
            {
                if ( $result == true )
                    $app->enqueueMessage(JText::sprintf('PLG_CONTENT_KRAKENIMAGE_DATABASE_DELETE_SUCCESS', JFile::getName($object_file->filepath)), 'Message');
                else
                    $app->enqueueMessage(JText::sprintf('PLG_CONTENT_KRAKENIMAGE_DATABASE_DELETE_FAIL', JFile::getName($object_file->filepath)), 'Warning');
            }

        }
        
        return true;
	}
    
    // Checks to see if a file exists in the krakenImage database
    // Returns the id of the database record if the file is in the krakenImage database
    // Otherwise it returns 0 if the file does not exist in the krakenImage database
    function getFileDatabaseId( $filepath )
    {
        $fileId = 0;
        
        // Get the database
   		$db = JFactory::getDBO();
                
        // check to see if the file is in the database
        $query = $db->getQuery(true);
        $query->select($db->quoteName('id'));
        $query->from($db->quoteName(IMAGE_SERVICE_DATABASE));
		$query->where($db->quoteName('filePath') . ' = ' . $db->quote($filepath));
        $db->setQuery((string)$query);
        $db->query();
        $fileRow = $db->loadRow( );
        
        if ($fileRow)
        {
            // record exists
            // update new reduced size and last reduced date
            $fileId = $fileRow['0'];
        }
        
        return $fileId;
    }
    
    // Update the krakenImage database by either
    // inserting a new file record or updating an existing one
    function updateDatabase( $filepath, $originalFilesize, $newFilesize, $status )
    {
        // Get the database
   		$db = JFactory::getDbo();
   		
        $fileId = $this->getFileDatabaseId($filepath);
        
        if ($fileId != 0)
        {
            // record exists, now update record
            $query = $db->getQuery(true);  
                      
            // Fields to update.
            $date = JFactory::getDate()->toSql(false, $db);
            $fields = array(
                $db->quoteName('reducedFileSize') . ' = ' . $db->quote($newFilesize),
                $db->quoteName('lastReduceDate') . ' = ' . $db->quote($date),
                $db->quoteName('lastReduceStatus') . ' = ' . $db->quote($status)
                );
                
            $conditions = array( $db->quoteName('id') . ' = ' . $db->quote($fileId) );
 
            $query->update($db->quoteName(IMAGE_SERVICE_DATABASE))->set($fields)->where($conditions); 
            $db->setQuery($query); 
            $result = $db->query();
        }
        else
        {
            // new file to database, insert new record 
            $query = $db->getQuery(true);                        
            $columns = array($db->quoteName('createDate'), $db->quoteName('filePath'), $db->quoteName('originalFileSize'), $db->quoteName('reducedFileSize'), $db->quoteName('lastReduceDate'), $db->quoteName('lastReduceStatus'));
            $date = JFactory::getDate()->toSql(false, $db);
            $values = array($db->quote($date), $db->quote($filepath), $db->quote($originalFilesize), $db->quote($newFilesize), $db->quote($date), $db->quote($status));
            $query
                ->insert($db->quoteName(IMAGE_SERVICE_DATABASE))
                ->columns($columns)
                ->values(implode(',', $values));
            $db->setQuery($query);
            $result = $db->query();            
        }
        
        return $result;
    }
    
    // Deletes a file from the krakenImage database
    // where the 'id' equals the passed in $fileId
    function deleteFileFromDatabase( $fileId )
    {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);
        // delete record where id = $fileId
        $conditions = array( $db->quoteName('id') . ' = ' . $db->quote($fileId) );
        $query
            ->delete($db->quoteName(IMAGE_SERVICE_DATABASE))
            ->where($conditions);
        $db->setQuery($query);
        $result = $db->query();
        return $result;
    }
    
    // This function makes sure json processing code is present and calls it.
    function json_decode_message($data)
    {
        if ( function_exists('json_decode') )
        {
            $data = json_decode( $data );
        }
        else
        {
            require_once( 'JSON/JSON.php' );
            $json = new Services_JSON( );
            $data = $json->decode( $data );
        }
        return $data;
    }
     
} // end plgContentkrakenImage

class krakenImageHelper
{
    // Determine if the image type is handled by kraken Image.
    // Currently we handle JPG, PNG, and GIF.
    public static function file_is_handled_by_image_service( $file )
    {
        $ext = strtolower(JFile::getExt($file));
		switch ($ext)
		{
			// Image
			case 'jpg':
            case 'jpeg':
			case 'png':
			case 'gif':
                return true;
            default:
                return false;
        }						
    }
    
    public static function getRelativeMediaPath($filePath, $requireLeadingDirSeparator=true)
    {
        $com_media_base = COM_MEDIA_BASE;
        if (!$requireLeadingDirSeparator)
            $com_media_base .= '/';
        $mediaBase = str_replace(DIRECTORY_SEPARATOR, '/', $com_media_base);
        $relativePath = str_replace($mediaBase, '', $filePath);
        return $relativePath;
    }
    
    public static function setkrakenImageParam($paramName, $value)
    {
        // Get the params and set the new values
        $params = JComponentHelper::getParams('com_krakenimage');
        $params->set($paramName, $value);

        // Get a new database query instance
        $db = JFactory::getDBO();
        $query = $db->getQuery(true);

        // Build the query
        $query->update('#__extensions AS a');
        $query->set('a.params = ' . $db->quote((string)$params));
        $query->where('a.element = "com_krakenimage"');

        // Execute the query
        $db->setQuery($query); 
        $db->query();
    }

    // Check if kraken Image Component is installed and enabled
    // Component is not installed if the return value is null
    // Otherwise, the enabled status of the kraken Image Component 
    public static function iskrakenImageComponentEnabled()
    {
        // Check if kraken Image Component is installed and enabled
        $component = 'com_krakenimage';
        $db = JFactory::getDbo();
        $dbTable = '#__extensions';
        $conditions = array( $db->quoteName('name') . ' = ' . $db->quote($component) ); 
        $query = $db->getQuery(true);
        $query->select($db->quoteName('enabled'))
              ->from($db->quoteName($dbTable))
              ->where($conditions);
        $db->setQuery((string)$query);
        $is_enabled = $db->loadResult();
        
        return $is_enabled;        
    }
    
} // end krakenImageHelper

?>
