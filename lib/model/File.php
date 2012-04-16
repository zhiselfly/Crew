<?php



/**
 * Skeleton subclass for representing a row from the 'file' table.
 *
 * 
 *
 * This class was autogenerated by Propel 1.5.6 on:
 *
 * Mon Oct 24 09:36:19 2011
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package    propel.generator.lib.model
 */
class File extends BaseFile
{
  /**
   * @return string
   */
  public function __toString()
  {
    return $this->getFilename();
  }

  public function changeStatus($newStatus, $user, $con = null)
  {
    $con = (is_null($con)) ? Propel::getConnection() : $con;
    $con->beginTransaction();

    try
    {
      $this
        ->setStatus($newStatus)
        ->setCommitStatusChanged($this->getLastChangeCommit())
        ->setUserStatusChanged($user)
        ->setDateStatusChanged(time())
        ->setReviewRequest(false)
        ->save($con)
      ;

      if($newStatus != BranchPeer::A_TRAITER)
      {
        $this->getBranch()->setReviewRequest(false)->save();
      }

      $con->commit();
    }
    catch (Exception $e)
    {
      $con->rollBack();
      throw new $e;
    }
  }

  /**
   * @static
   * @param int $userId
   * @param int $repositoryId
   * @param int $branchId
   * @param int $fileId
   * @param int $oldStatus
   * @param int $newStatus
   * @param string $message
   * @return int
   */
  public static function saveAction($userId, $repositoryId, $branchId, $fileId, $oldStatus, $newStatus, $message = 'status was changed from <strong>%s</strong> to <strong>%s</strong>')
  {
    if ($oldStatus === $newStatus)
    {
      return 0;
    }
    
    $file = FileQuery::create()->filterById($fileId)->findOne();
    if(!$file)
    {
      return false;
    }
    
    $statusAction = new StatusAction();
    return $statusAction
      ->setUserId($userId)
      ->setRepositoryId($repositoryId)
      ->setBranchId($branchId)
      ->setFileId($fileId)
      ->setOldStatus($oldStatus)
      ->setNewStatus($newStatus)
      ->setMessage(sprintf($message, BranchPeer::getLabelStatus($oldStatus), BranchPeer::getLabelStatus($newStatus), $file->getFilename(), $file->getBranch()->__toString()))
      ->save()
    ;
  }

  public function setCommitInfos($commitInfos)
  {
    $explodedInfos = (strlen($commitInfos) > 0) ? explode(' ', $commitInfos, 2) : array();
    if(count($explodedInfos) == 2)
    {
      $this->setLastChangeCommitDesc($explodedInfos[1]);
      $profile = ProfilePeer::getProfileByEmail($explodedInfos[0]);
      if($profile)
      {
        $this->setLastChangeCommitUser($profile->getSfGuardUserId());
      }
    }
    return $this;
  }
} // File
