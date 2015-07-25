<?php namespace Matome\Storage;

use Matome\Metadata\MetadataGenerator;
use Matome\Input\Transformer;

class DocRepository extends StorageAbstract {

  protected $relations = array('enactment', 'unappliedEffect');

  /**
   * Constructor
   * 
   * @param Publisher $model
   */
  public function __construct() {
    $this->model = new \Doc;
    $this->metadataGenerator = new MetadataGenerator();
    $this->transformer = new Transformer();
  }

  public function getMetadataById($id) {
    return $this->getById($id, $this->relations);
  }

  /**
   * Store new metadata
   * @param  object $xml
   * @return boolean     
   */
  public function storeMetadata($xml, $cron = false) {
    try {
      if(!$cron) {
        $split = explode('.', $xml['filename']);
        $filename = $split[0].'.xml';
        $this->generateMetadataFile($xml, $filename);
      }
      // insert to docs
      $inputDoc = $this->transformer->doc($xml);
      // get creator
      $creator = \Creator::where('name', $inputDoc['creator'])->first();
      $inputDoc['creatorId'] = $creator['id'];
      $doc = $this->model->create($inputDoc);

      // insert to enactments
      $doc = $this->model->find($doc['id']);
      $enactments = $this->transformer->enactments($xml);
      if(!empty($enactments)) {
        $doc->enactment()->create((array)$enactments);
      }
      
      // insert to unappliedEffects
      $unappliedEffects = $this->transformer->unappliedEffects($xml);
      if(!empty($unappliedEffects)) {
        if(is_object($unappliedEffects)) {
          $doc->unappliedEffect()->create((array)$unappliedEffects);
        } else {
          $unapplieds = array();
          foreach ($unappliedEffects as $value) {
            $item = new \UnappliedEffect((array)$value);
            array_push($unapplieds, $item);
          }
          $doc->unappliedEffect()->saveMany($unapplieds);
        }
      }

    } catch (Exception $e) {
      return false;
    }

    return true;
  }

  public function update($input, $filename) {
    $doc = $this->model->where('filename', $filename)->first();
    $doc->fill($input);
    $doc->save();

    return $doc;
  }

  /**
   * Update metadata
   * @param  array $xml
   * @param  string $id 
   * @return boolean    
   */
  public function updateMetadata($xml, $id = null, $cron = false) {
    try {
      if(!$cron) {
        $split = explode('.', $xml['filename']);
        $filename = $split[0].'.xml';
        $this->generateMetadataFile($xml, $filename);
      }
      // update doc
      $docs = $this->transformer->doc($xml);
      $creator = \Creator::where('name', $docs['creator'])->first();
      $docs['creatorId'] = $creator['id'];

      if($id) {
        $saveDoc = $this->model->find($id)->update($docs);
        $saveDoc = $this->model->find($id);
      } else {
        $saveDoc = $this->update((array)$docs, $docs['filename']);
      }

      // update enactment
      $enactments = $this->transformer->enactments($xml);

      if(!empty($enactments)) {
        $enact = \Enactment::where('docId', $saveDoc['id'])->first();
        if(!empty($enact)) {
          $enact->number = $enactments['number'];
          $enact->publishedDate = $enactments['publishedDate'];
          $enact->identifier = $enactments['identifier'];

          $enact->save();
        } else {
          $this->model->find($saveDoc['id'])->enactment()->create($enactments);
        }
      }

      // update unapplied
      $unappliedEffects = $this->transformer->unappliedEffects($xml);

      if(!empty($unappliedEffects)) {
        $unapplieds = array();
        foreach($unappliedEffects as $value) {
          $item = new \UnappliedEffect((array)$value);
          array_push($unapplieds, $item);
        }
        $this->model->find($saveDoc['id'])->unappliedEffect()->delete();
        $this->model->find($saveDoc['id'])->unappliedEffect()->saveMany($unapplieds);
      }

    } catch (Exception $e) {
      return false;
    }
    return true;
  }

  /**
   * Generate metadata file
   * 
   * @return json
   */
  public function generateMetadataFile($metadata, $filename) {
    $generate = $this->metadataGenerator->storeMetadata($metadata, $filename, public_path().'/files/');
    if($generate) {
      return true;
    }
    return false;
  }

  /**
   * Get all metadatas
   * 
   * @param  integer $items
   * @param  array   $with 
   * @return Paginator       
   */
  public function pagination($items = 30, array $order = array(), array $with = array()) {

    if(empty($order)) {
      $entity = $this->make($with);
    } else {
      $entity = $this->make($with)->orderBy($order['orderBy'], $order['order']);
    }
    
    return $entity->paginate($items);
  }

  /**
   * Get complete metadatas
   * @param  integer $items
   * @param  array   $with 
   * @return Paginator        
   */
  
  public function getCompleteMetadatas($items = 30, array $order = array(), array $with = array()) {

    if(empty($order)) {
      $metadatas = $this->make($with)->where('title', '!=', '')->where('filename','!=', '')->where('originalFilename', '!=', '')->where('number', '!=', '')->where('docType', '!=', '')
      ->where('publishedDate', '!=', '0000-00-00')->where('validDate', '!=', '0000-00-00')->where('identifier', '!=', '')->where('description', '!=','')->where('subject','!=', '');
    } else {
      $metadatas = $this->make($with)->where('title', '!=', '')->where('filename','!=', '')->where('originalFilename', '!=', '')->where('number', '!=', '')->where('docType', '!=', '')
      ->where('publishedDate', '!=', '0000-00-00')->where('validDate', '!=', '0000-00-00')->where('identifier', '!=', '')->where('description', '!=','')->where('subject','!=', '')->orderBy($order['orderBy'], $order['order']);
    }
    
    return $metadatas->paginate($items);
  }

  /**
   * Get uncomplete metadatas
   * 
   * @param  integer $items 
   * @param  array   $with 
   * @return Paginator        
   */

  public function getUncompleteMetadatas($items = 30, array $order = array(), array $with = array()) {
    if(empty($order)) {
      $metadatas = $this->make($with)->where('title', '')->orWhere('filename', '')->orWhere('originalFilename', '')->orWhere('number', '')->orWhere('docType', '')
      ->orWhere('publishedDate', '0000-00-00')->orWhere('validDate', '0000-00-00')->orWhere('identifier', '')->orWhere('description', '')->orWhere('subject', '');
    } else {
      $metadatas = $this->make($with)->where('title', '')->orWhere('filename', '')->orWhere('originalFilename', '')->orWhere('number', '')->orWhere('docType', '')
      ->orWhere('publishedDate', '0000-00-00')->orWhere('validDate', '0000-00-00')->orWhere('identifier', '')->orWhere('description', '')->orWhere('subject', '')->orderBy($order['orderBy'], $order['order']);
    }
    return $metadatas->paginate($items);
  }

  /**
   * Get total complete metadatas
   * 
   * @param  array  $with
   * @return integer     
   */

  public function completeMetadatasCount(array $with = array()) {
    return $this->make($with)->where('title', '!=', '')->where('filename','!=', '')->where('originalFilename', '!=', '')->where('number', '!=', '')->where('docType', '!=', '')
    ->where('publishedDate', '!=', '0000-00-00')->where('validDate', '!=', '0000-00-00')->where('identifier', '!=', '')->where('description', '!=','')->where('subject','!=', '')->count();
  }

  /**
   * Get total uncomplete metadatas
   * 
   * @param  array  $with
   * @return integer      
   */

  public function uncompleteMetadatasCount(array $with = array()) {
    return $this->make($with)->where('title', '')->orWhere('filename', '')->orWhere('originalFilename', '')->orWhere('number', '')->orWhere('docType', '')
    ->orWhere('publishedDate', '0000-00-00')->orWhere('validDate', '0000-00-00')->orWhere('identifier', '')->orWhere('description', '')->orWhere('subject', '')->count();
  }

}