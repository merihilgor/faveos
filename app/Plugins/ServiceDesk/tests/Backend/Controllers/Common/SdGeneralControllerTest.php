<?php

namespace App\Plugins\ServiceDesk\Tests\Unit\Backend\Common;

use Tests\AddOnTestCase;
use App\Plugins\ServiceDesk\Model\Changes\SdChanges;
use App\Plugins\ServiceDesk\Model\Common\GeneralInfo;
use App\Plugins\ServiceDesk\Model\Problem\SdProblem;

class SdGeneralControllerTest extends AddOnTestCase
{

    /** @group createUpdatePopup */
    public function test_createUpdatePopup_withIdentifierAsReasonAndDescription_returnsUpdatedSuccessfully()
    {
        $this->getLoggedInUserForWeb('agent');
        $changeId = factory(SdChanges::class)->create()->id;
        $tableName = 'sd_changes';
        $identifier = 'reason';
        $description = "Restore System";
        $response = $this->call('POST', url("service-desk/api/general-popup/$changeId/$tableName"), ['description' => $description, 'identifier' => $identifier]);
        $response->assertStatus(200);
        $this->assertDatabaseHas('sd_gerneral', ['key' => $identifier, 'value' => $description, 'owner' => $tableName . ':' . $changeId]);
    }

    /** @group createUpdatePopup */
    public function test_createUpdatePopup_withIdentifierAsBackoutPlanAndDescription_returnsUpdatedSuccessfully()
    {
        $this->getLoggedInUserForWeb('agent');
        $changeId = factory(SdChanges::class)->create()->id;
        $tableName = 'sd_changes';
        $identifier = 'backout-plan';
        $description = "Restore System";
        $response = $this->call('POST', url("service-desk/api/general-popup/$changeId/$tableName"), ['description' => $description, 'identifier' => $identifier]);
        $response->assertStatus(200);
        $this->assertDatabaseHas('sd_gerneral', ['key' => $identifier, 'value' => $description, 'owner' => $tableName . ':' . $changeId]);
    }

    /** @group createUpdatePopup */
    public function test_createUpdatePopup_withIdentifierAsImpactAndDescription_returnsUpdatedSuccessfully()
    {
        $this->getLoggedInUserForWeb('agent');
        $changeId = factory(SdChanges::class)->create()->id;
        $tableName = 'sd_changes';
        $identifier = 'impact';
        $description = "Restore System";
        $response = $this->call('POST', url("service-desk/api/general-popup/$changeId/$tableName"), ['description' => $description, 'identifier' => $identifier]);
        $response->assertStatus(200);
        $this->assertDatabaseHas('sd_gerneral', ['key' => $identifier, 'value' => $description, 'owner' => $tableName . ':' . $changeId]);
    }


    /** @group createUpdatePopup */
    public function test_createUpdatePopup_withIdentifierAsRollOutPlanAndDescription_returnsUpdatedSuccessfully()
    {
        $this->getLoggedInUserForWeb('agent');
        $changeId = factory(SdChanges::class)->create()->id;
        $tableName = 'sd_changes';
        $identifier = 'rollout-plan';
        $description = "Restore System";
        $response = $this->call('POST', url("service-desk/api/general-popup/$changeId/$tableName"), ['description' => $description, 'identifier' => $identifier]);
        $response->assertStatus(200);
        $this->assertDatabaseHas('sd_gerneral', ['key' => $identifier, 'value' => $description, 'owner' => $tableName . ':' . $changeId]);
    }

     /** @group editPopup */
    public function test_editPopup_withIdentifierAsRollOutPlanAndDescription_returnsWrongDetails()
    {
        $this->getLoggedInUserForWeb('agent');
        $tableName = 'sd_changes';
        $changeId = factory(SdChanges::class)->create()->id;
        $owner = "sd_changes:{$changeId}";
        $identifier = 'rollout-plan';
        $description = 'proceed with another update';
        GeneralInfo::create(['owner' => $owner, 'key' => $identifier, 'value' => $description]);
        $wrongChangeId = 'wrong';
        $response = $this->call('GET', url("service-desk/api/general-popup/$wrongChangeId/$tableName/$identifier"));
        $response->assertStatus(400);
    }


    /** @group editPopup */
    public function test_editPopup_withIdentifierAsRollOutPlanAndDescription_returnsPopupDetails()
    {
        $this->getLoggedInUserForWeb('agent');
        $tableName = 'sd_changes';
        $changeId = factory(SdChanges::class)->create()->id;
        $owner = "sd_changes:{$changeId}";
        $identifier = 'rollout-plan';
        $description = 'proceed with another update';
        GeneralInfo::create(['owner' => $owner, 'key' => $identifier, 'value' => $description]);
        $response = $this->call('GET', url("service-desk/api/general-popup/$changeId/$tableName/$identifier"));
        $response->assertStatus(200);
        $this->assertDatabaseHas('sd_gerneral', ['key' => $identifier, 'value' => $description, 'owner' => $tableName . ':' . $changeId]);
    }

     /** @group deletePopupDetails */
    public function test_deletePopupDetails_wittWrongChangeId_returnsWrongDetails()
    {
        $this->getLoggedInUserForWeb('agent');
        $tableName = 'sd_changes';
        $changeId = factory(SdChanges::class)->create()->id;
        $owner = "sd_changes:{$changeId}";
        $identifier = 'rollout-plan';
        $description = 'proceed with another update';
        GeneralInfo::create(['owner' => $owner, 'key' => $identifier, 'value' => $description]);
        $wrongChangeId = 'wrong';
        $response = $this->call('DELETE', url("service-desk/api/delete/general-popup/$wrongChangeId/$tableName/$identifier"));
        $response->assertStatus(400);
    }

    /** @group deletePopupDetails */
    public function test_deletePopupDetails_withIdentifierAsRollOutPlanAndDescription_returnsDeletedSuccessfully()
    {
        $this->getLoggedInUserForWeb('agent');
        $tableName = 'sd_changes';
        $changeId = factory(SdChanges::class)->create()->id;
        $owner = "sd_changes:{$changeId}";
        $identifier = 'rollout-plan';
        $description = 'proceed with another update';
        GeneralInfo::create(['owner' => $owner, 'key' => $identifier, 'value' => $description]);
        $response = $this->call('DELETE', url("service-desk/api/delete/general-popup/$changeId/$tableName/$identifier"));
        $response->assertStatus(200);
        $this->assertDatabaseMissing('sd_gerneral', ['key' => $identifier, 'value' => $description, 'owner' => $tableName . ':' . $changeId]);
    }

    /** @group createUpdatePopup */
    public function test_createUpdatePopup_withIdentifierAsRootCauseAndDescription_returnsUpdatedSuccessfully()
    {
        $this->getLoggedInUserForWeb('agent');
        $problemId = factory(SdProblem::class)->create()->id;
        $tableName = 'sd_problem';
        $identifier = 'root-cause';
        $description = "Restore System";
        $response = $this->call('POST', url("service-desk/api/general-popup/$problemId/$tableName"), ['description' => $description, 'identifier' => $identifier]);
        $response->assertStatus(200);
        $this->assertDatabaseHas('sd_gerneral', ['key' => $identifier, 'value' => $description, 'owner' => $tableName . ':' . $problemId]);
    }

    /** @group createUpdatePopup */
    public function test_createUpdatePopup_withIdentifierAsProblemImpactAndDescription_returnsUpdatedSuccessfully()
    {
        $this->getLoggedInUserForWeb('agent');
        $problemId = factory(SdProblem::class)->create()->id;
        $tableName = 'sd_problem';
        $identifier = 'impact';
        $description = "Restore System";
        $response = $this->call('POST', url("service-desk/api/general-popup/$problemId/$tableName"), ['description' => $description, 'identifier' => $identifier]);
        $response->assertStatus(200);
        $this->assertDatabaseHas('sd_gerneral', ['key' => $identifier, 'value' => $description, 'owner' => $tableName . ':' . $problemId]);
    }

    /** @group createUpdatePopup */
    public function test_createUpdatePopup_withIdentifierAsSymptomsAndDescription_returnsUpdatedSuccessfully()
    {
        $this->getLoggedInUserForWeb('agent');
        $problemId = factory(SdProblem::class)->create()->id;
        $tableName = 'sd_problem';
        $identifier = 'symptoms';
        $description = "Restore System";
        $response = $this->call('POST', url("service-desk/api/general-popup/$problemId/$tableName"), ['description' => $description, 'identifier' => $identifier]);
        $response->assertStatus(200);
        $this->assertDatabaseHas('sd_gerneral', ['key' => $identifier, 'value' => $description, 'owner' => $tableName . ':' . $problemId]);
    }

    /** @group createUpdatePopup */
    public function test_createUpdatePopup_withIdentifierAsSolutionAndDescription_returnsUpdatedSuccessfully()
    {
        $this->getLoggedInUserForWeb('agent');
        $problemId = factory(SdProblem::class)->create()->id;
        $tableName = 'sd_problem';
        $identifier = 'solution';
        $description = "Restore System";
        $title = 'solution-title';
        $response = $this->call('POST', url("service-desk/api/general-popup/$problemId/$tableName"), ['description' => $description, 'identifier' => $identifier,'title' => $title]);
        $response->assertStatus(200);
        $this->assertDatabaseHas('sd_gerneral', ['key' => $identifier, 'value' => $description, 'owner' => $tableName . ':' . $problemId]);
        $this->assertDatabaseHas('sd_gerneral', ['key' => 'solution-title', 'value' => $title, 'owner' => $tableName . ':' . $problemId]);
    }

    /** @group createUpdatePopup */
    public function test_createUpdatePopup_withIdentifierAsSolutionAndDescriptionAndTitleFailingValidation_returnsUpdatedSuccessfully()
    {
    $this->getLoggedInUserForWeb('agent');
        $problemId = factory(SdProblem::class)->create()->id;
        $tableName = 'sd_problem';
        $identifier = 'solution';
        $description = "Restore System";
        $title = 'solution-titleqwertyuiopsdfghjklsdfghjklfghjksdfjkwertyklasdfgh,.zxcvbn m,wertjksdfbnmsdvbnmasdfghjsdvbnsdfbnxcvbnxcvbntcgnxgexyqwfdtqwyfdxuqwydfxeuywqdfeyuwdefudcefchyefvulcyfevycfgecuyfevcuyegvwcyvvvvydwefuydfwydefuwdfv';
        $response = $this->call('POST', url("service-desk/api/general-popup/$problemId/$tableName"), ['description' => $description, 'identifier' => $identifier,'title' => $title]);
        $response->assertStatus(200);
    }

    /** @group editPopup */
    public function test_editPopup_withIdentifierAsRootCauseAndDescriptionAndWithWrongProblemId_returnsWrongDetails()
    {
        $this->getLoggedInUserForWeb('agent');
        $tableName = 'sd_problem';
        $problemId = factory(SdProblem::class)->create()->id;
        $owner = "sd_problem:{$problemId}";
        $identifier = 'root-cause';
        $description = 'proceed with another update';
        GeneralInfo::create(['owner' => $owner, 'key' => $identifier, 'value' => $description]);
        $wrongProblemId = 'wrong';
        $response = $this->call('GET', url("service-desk/api/general-popup/$wrongProblemId/$tableName/$identifier"));
        $response->assertStatus(400);
    }

    /** @group editPopup */
    public function test_editPopup_withIdentifierAsRootCausePlanAndDescription_returnsPopupDetails()
    {
        $this->getLoggedInUserForWeb('agent');
        $tableName = 'sd_problem';
        $problemId = factory(SdProblem::class)->create()->id;
        $owner = "sd_problem:{$problemId}";
        $identifier = 'root-cause';
        $description = 'proceed with another update';
        GeneralInfo::create(['owner' => $owner, 'key' => $identifier, 'value' => $description]);
        $response = $this->call('GET', url("service-desk/api/general-popup/$problemId/$tableName/$identifier"));
        $response->assertStatus(200);
        $this->assertDatabaseHas('sd_gerneral', ['key' => $identifier, 'value' => $description, 'owner' => $tableName . ':' . $problemId]);
    }

    /** @group deletePopupDetails */
    public function test_deletePopupDetails_withWrongProblemId_returnsWrongDetails()
    {
        $this->getLoggedInUserForWeb('agent');
        $tableName = 'sd_problem';
        $problemId = factory(SdProblem::class)->create()->id;
        $owner = "sd_problem:{$problemId}";
        $identifier = 'root-cause';
        $description = 'proceed with another update';
        GeneralInfo::create(['owner' => $owner, 'key' => $identifier, 'value' => $description]);
        $wrongProblemId = 'wrong';
        $response = $this->call('DELETE', url("service-desk/api/delete/general-popup/$wrongProblemId/$tableName/$identifier"));
        $response->assertStatus(400);
    }

    /** @group deletePopupDetails */
    public function test_deletePopupDetails_withIdentifierAsRootCauseAndDescription_returnsDeletedSuccessfully()
    {
        $this->getLoggedInUserForWeb('agent');
        $tableName = 'sd_problem';
        $problemId = factory(SdProblem::class)->create()->id;
        $owner = "sd_problem:{$problemId}";
        $identifier = 'root-cause';
        $description = 'proceed with another update';
        GeneralInfo::create(['owner' => $owner, 'key' => $identifier, 'value' => $description]);
        $response = $this->call('DELETE', url("service-desk/api/delete/general-popup/$problemId/$tableName/$identifier"));
        $response->assertStatus(200);
        $this->assertDatabaseMissing('sd_gerneral', ['key' => $identifier, 'value' => $description, 'owner' => $tableName . ':' . $problemId]);
    }


}