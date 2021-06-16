<?php

namespace App\Plugins\ServiceDesk\tests\Backend\Controllers;

use Tests\AddOnTestCase;
use Illuminate\Http\UploadedFile;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Plugins\ServiceDesk\Model\Assets\BarcodeTemplate;

class BarcodeTemplateControllerTest extends AddOnTestCase
{
    private $data = [
        'width' => '2',
        'height' => '1',
        'labels_per_row' => '5',
        'space_between_labels' => '5',
        'display_logo_confirmed' => 1,
    ];

    /** @group store */
    public function test_storeMethod_CreatesTheTemplate_ForSuccess()
    {
        $this->getLoggedInUserForWeb('admin');
        $response = $this->call('POST',url('service-desk/barcode/template/create'),$this->data+['logo_image' => UploadedFile::fake()->image('avatar.jpg')]);
        $response->assertOk();
        $this->assertDatabaseHas('sd_barcode_templates',['width' => 2, 'space_between_labels' => 5]);

    }

    /** @group store */
    public function test_storeMethod_WithMissingRequiredParameters_ForFailure()
    {
        $this->getLoggedInUserForWeb('admin');
        $response = $this->call(
            'POST',
            url(
                'service-desk/barcode/template/create'
            ),array_diff($this->data,['2'])
        );
        $response->assertStatus(412)
        ->assertJsonFragment(["width"=>"This field is required"]);
        $this->assertDatabaseMissing(
            'sd_barcode_templates',['width' => 2, 'space_between_labels' => 5]
        );

    }

    /** @group index */
    public function test_indexMethod_ShouldReturnBarcodeTemplates()
    {
        $this->getLoggedInUserForWeb('admin');
        factory(BarcodeTemplate::class,1)->create();
        $response = $this->call('GET','service-desk/barcode-template/');
        $response->assertOk()
        ->assertJsonFragment([
            'success' => true,
            'width' => "2",
        ]);
    }

    /** @group update */
    public function test_updateMethod_withRequiredParams_ForSuccess()
    {
        $this->getLoggedInUserForWeb('admin');
        $template = factory(BarcodeTemplate::class,1)->create(['width' => 2,'height' => 1]);
        $response = $this->call('POST','service-desk/barcode/template/update/'.$template->first()->toArray()['id'],$this->data);
        $response->assertOk();
        $this->assertDatabaseHas('sd_barcode_templates',['width' => 2, 'space_between_labels' => 5]);

    }

    /** @group delete */
    public function test_deleteMethod_WithValidTemplate_ForSuccess()
    {
        $this->getLoggedInUserForWeb('admin');
        $template = factory(BarcodeTemplate::class,1)->create(['width' => 1000]);
        $response = $this->call('DELETE','service-desk/barcode/template/delete/'.$template->first()->toArray()['id']);
        $response->assertOk();
        $this->assertDatabaseMissing('sd_barcode_templates',['width' => 1000]);
    }

    /** @group delete */
    public function test_deleteMethod_withInvalidTemplate_Fails()
    {
        $this->withoutExceptionHandling();
        try {
            $this->json('DELETE', 'service-desk/barcode/template/delete/99901');
        } catch (ModelNotFoundException $exception) {
            $this->assertEquals('No query results for model [App\Plugins\ServiceDesk\Model\Assets\BarcodeTemplate] 99901', $exception->getMessage());
            return;
        }
    }
}