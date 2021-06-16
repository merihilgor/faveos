<?php

/**
 * Products
 */
if (!Breadcrumbs::exists('service-desk.products.index')) {
    Breadcrumbs::register('service-desk.products.index', function($breadcrumbs) {
        $breadcrumbs->parent('setting');
        $breadcrumbs->push(Lang::get('ServiceDesk::lang.products'), route('service-desk.products.index'));
    });
}
if (!Breadcrumbs::exists('service-desk.products.create')) {
    Breadcrumbs::register('service-desk.products.create', function($breadcrumbs) {
        $breadcrumbs->parent('service-desk.products.index');
        $breadcrumbs->push(Lang::get('ServiceDesk::lang.create'), route('service-desk.products.create'));
    });
}
if (!Breadcrumbs::exists('service-desk.products.edit')) {
    Breadcrumbs::register('service-desk.products.edit', function($breadcrumbs, $id) {
        $breadcrumbs->parent('service-desk.products.index');
        $breadcrumbs->push(Lang::get('ServiceDesk::lang.edit'), route('service-desk.products.edit', $id));
    });
}
if (!Breadcrumbs::exists('service-desk.products.show')) {
    Breadcrumbs::register('service-desk.products.show', function($breadcrumbs, $id) {
        $breadcrumbs->parent('service-desk.products.index');
        $breadcrumbs->push(Lang::get('ServiceDesk::lang.view'), route('service-desk.products.show', $id));
    });
}
if (!Breadcrumbs::exists('service-desk.contract.index')) {

    /**
     * Contract
     */
    Breadcrumbs::register('service-desk.contract.index', function($breadcrumbs) {
        $breadcrumbs->parent('dashboard');
        $breadcrumbs->push(Lang::get('ServiceDesk::lang.contract'), route('service-desk.contract.index'));
    });
}
if (!Breadcrumbs::exists('service-desk.contract.create')) {
    Breadcrumbs::register('service-desk.contract.create', function($breadcrumbs) {
        $breadcrumbs->parent('service-desk.contract.index');
        $breadcrumbs->push(Lang::get('ServiceDesk::lang.create'), route('service-desk.contract.create'));
    });
}
if (!Breadcrumbs::exists('service-desk.contract.edit')) {
    Breadcrumbs::register('service-desk.contract.edit', function($breadcrumbs, $id) {
        $breadcrumbs->parent('service-desk.contract.index');
        $breadcrumbs->push(Lang::get('ServiceDesk::lang.edit'), route('service-desk.contract.edit', $id));
    });
}
if (!Breadcrumbs::exists('service-desk.contract.show')) {
    Breadcrumbs::register('service-desk.contract.show', function($breadcrumbs, $id) {
        $breadcrumbs->parent('service-desk.contract.index');
        $breadcrumbs->push(Lang::get('ServiceDesk::lang.view'), route('service-desk.contract.show', $id));
    });
}
if (!Breadcrumbs::exists('service-desk.vendor.index')) {

    /**
     * Vendor
     */
    Breadcrumbs::register('service-desk.vendor.index', function($breadcrumbs) {
        $breadcrumbs->parent('setting');
        $breadcrumbs->push(Lang::get('ServiceDesk::lang.vendor'), route('service-desk.vendor.index'));
    });
}
if (!Breadcrumbs::exists('service-desk.vendor.create')) {
    Breadcrumbs::register('service-desk.vendor.create', function($breadcrumbs) {
        $breadcrumbs->parent('service-desk.vendor.index');
        $breadcrumbs->push(Lang::get('ServiceDesk::lang.create'), route('service-desk.vendor.create'));
    });
}
if (!Breadcrumbs::exists('service-desk.vendor.edit')) {
    Breadcrumbs::register('service-desk.vendor.edit', function($breadcrumbs, $id) {
        $breadcrumbs->parent('service-desk.vendor.index');
        $breadcrumbs->push(Lang::get('ServiceDesk::lang.edit'), route('service-desk.vendor.edit', $id));
    });
}
if (!Breadcrumbs::exists('service-desk.vendor.show')) {
    Breadcrumbs::register('service-desk.vendor.show', function($breadcrumbs, $id) {
        $breadcrumbs->parent('service-desk.vendor.index');
        $breadcrumbs->push(Lang::get('ServiceDesk::lang.view'), route('service-desk.vendor.show', $id));
    });
}
if (!Breadcrumbs::exists('service-desk.assetstypes.index')) {

    /**
     * Asset Type
     */
    Breadcrumbs::register('service-desk.assetstypes.index', function($breadcrumbs) {
        $breadcrumbs->parent('setting');
        $breadcrumbs->push(Lang::get('ServiceDesk::lang.assetstypes'), route('service-desk.assetstypes.index'));
    });
}
if (!Breadcrumbs::exists('service-desk.assetstypes.create')) {
    Breadcrumbs::register('service-desk.assetstypes.create', function($breadcrumbs) {
        $breadcrumbs->parent('service-desk.assetstypes.index');
        $breadcrumbs->push(Lang::get('ServiceDesk::lang.create'), route('service-desk.assetstypes.create'));
    });
}
if (!Breadcrumbs::exists('service-desk.assetstypes.edit')) {
    Breadcrumbs::register('service-desk.assetstypes.edit', function($breadcrumbs, $id) {
        $breadcrumbs->parent('service-desk.assetstypes.index');
        $breadcrumbs->push(Lang::get('ServiceDesk::lang.edit'), route('service-desk.assetstypes.edit', $id));
    });
}
if (!Breadcrumbs::exists('service-desk.contractstypes.index')) {

    /**
     * Contract Type
     */
    Breadcrumbs::register('service-desk.contractstypes.index', function($breadcrumbs) {
        $breadcrumbs->parent('setting');
        $breadcrumbs->push(Lang::get('ServiceDesk::lang.contract_type'), route('service-desk.contractstypes.index'));
    });
}
if (!Breadcrumbs::exists('service-desk.contractstypes.create')) {
    Breadcrumbs::register('service-desk.contractstypes.create', function($breadcrumbs) {
        $breadcrumbs->parent('service-desk.contractstypes.index');
        $breadcrumbs->push(Lang::get('ServiceDesk::lang.create'), route('service-desk.contractstypes.create'));
    });
}
if (!Breadcrumbs::exists('service-desk.contractstypes.edit')) {

    Breadcrumbs::register('service-desk.contractstypes.edit', function($breadcrumbs, $id) {
        $breadcrumbs->parent('service-desk.contractstypes.index');
        $breadcrumbs->push(Lang::get('ServiceDesk::lang.edit'), route('service-desk.contractstypes.edit', $id));
    });
}
if (!Breadcrumbs::exists('service-desk.licensetypes.index')) {

    /**
     * License Type
     */
    Breadcrumbs::register('service-desk.licensetypes.index', function($breadcrumbs) {
        $breadcrumbs->parent('setting');
        $breadcrumbs->push(Lang::get('ServiceDesk::lang.license_type'), route('service-desk.licensetypes.index'));
    });
}
if (!Breadcrumbs::exists('service-desk.licensetypes.create')) {
    Breadcrumbs::register('service-desk.licensetypes.create', function($breadcrumbs) {
        $breadcrumbs->parent('service-desk.licensetypes.index');
        $breadcrumbs->push(Lang::get('ServiceDesk::lang.create'), route('service-desk.licensetypes.create'));
    });
}
if (!Breadcrumbs::exists('service-desk.licensetypes.edit')) {

    Breadcrumbs::register('service-desk.licensetypes.edit', function($breadcrumbs, $id) {
        $breadcrumbs->parent('service-desk.licensetypes.index');
        $breadcrumbs->push(Lang::get('ServiceDesk::lang.edit'), route('service-desk.licensetypes.edit', $id));
    });
}

if (!Breadcrumbs::exists('service-desk.procurment.index')) {

    /**
     * Product Procurement
     */
    Breadcrumbs::register('service-desk.procurment.index', function($breadcrumbs) {
        $breadcrumbs->parent('setting');
        $breadcrumbs->push(Lang::get('ServiceDesk::lang.product_procurment'), route('service-desk.procurment.index'));
    });
}
if (!Breadcrumbs::exists('service-desk.procurment.create')) {
    Breadcrumbs::register('service-desk.procurment.create', function($breadcrumbs) {
        $breadcrumbs->parent('service-desk.procurment.index');
        $breadcrumbs->push(Lang::get('ServiceDesk::lang.create'), route('service-desk.procurment.create'));
    });
}
if (!Breadcrumbs::exists('service-desk.procurment.edit')) {

    Breadcrumbs::register('service-desk.procurment.edit', function($breadcrumbs, $id) {
        $breadcrumbs->parent('service-desk.procurment.index');
        $breadcrumbs->push(Lang::get('ServiceDesk::lang.edit'), route('service-desk.procurment.edit', $id));
    });
}
/**
 * Form Group
 */
Breadcrumbs::register('form.group.index', function($breadcrumbs) {
    $breadcrumbs->parent('setting');
    $breadcrumbs->push(trans('lang.forms'), url('form-groups'));
});

Breadcrumbs::register('form.group.create', function($breadcrumbs) {
    $breadcrumbs->parent('form.group.index');
    $breadcrumbs->push(trans('ServiceDesk::lang.create'), route('form.group.create'));
});

Breadcrumbs::register('form.group.edit', function($breadcrumbs, $id) {
    $breadcrumbs->parent('form.group.index');
    $breadcrumbs->push(trans('ServiceDesk::lang.edit'), route('form.group.edit', $id));
});

if (!Breadcrumbs::exists('cabs.index')) {

    /**
     * Cab
     */
    Breadcrumbs::register('cabs.index', function($breadcrumbs) {
        $breadcrumbs->parent('setting');
        $breadcrumbs->push(Lang::get('ServiceDesk::lang.change_advisory_board'), route('cabs.index'));
    });
}
if (!Breadcrumbs::exists('cabs.create')) {
    Breadcrumbs::register('cabs.create', function($breadcrumbs) {
        $breadcrumbs->parent('cabs.index');
        $breadcrumbs->push(Lang::get('ServiceDesk::lang.create'), route('cabs.create'));
    });
}
if (!Breadcrumbs::exists('cabs.edit')) {

    Breadcrumbs::register('cabs.edit', function($breadcrumbs, $id) {
        $breadcrumbs->parent('cabs.index');
        $breadcrumbs->push(Lang::get('ServiceDesk::lang.edit'), route('cabs.edit', $id));
    });
}
if (!Breadcrumbs::exists('asset.index')) {

    /**
     * Assets
     */
    Breadcrumbs::register('service-desk.asset.index', function($breadcrumbs) {
        $breadcrumbs->parent('dashboard');
        $breadcrumbs->push(Lang::get('ServiceDesk::lang.asset'), route('service-desk.asset.index'));
    });
}
if (!Breadcrumbs::exists('service-desk.asset.create')) {
    Breadcrumbs::register('service-desk.asset.create', function($breadcrumbs) {
        $breadcrumbs->parent('service-desk.asset.index');
        $breadcrumbs->push(Lang::get('ServiceDesk::lang.create'), route('service-desk.asset.create'));
    });
}
if (!Breadcrumbs::exists('service-desk.asset.edit')) {

    Breadcrumbs::register('service-desk.asset.edit', function($breadcrumbs, $id) {
        $breadcrumbs->parent('service-desk.asset.index');
        $breadcrumbs->push(Lang::get('ServiceDesk::lang.edit'), route('service-desk.asset.edit', $id));
    });
}
if (!Breadcrumbs::exists('service-desk.asset.show')) {
    Breadcrumbs::register('service-desk.asset.show', function($breadcrumbs, $id) {
        $breadcrumbs->parent('service-desk.asset.index');
        $breadcrumbs->push(Lang::get('ServiceDesk::lang.view'), route('service-desk.asset.show', $id));
    });
}
if (!Breadcrumbs::exists('service-desk.releases.index')) {

    /**
     * Release
     */
    Breadcrumbs::register('service-desk.releases.index', function($breadcrumbs) {
        $breadcrumbs->parent('dashboard');
        $breadcrumbs->push(Lang::get('ServiceDesk::lang.release'), route('service-desk.releases.index'));
    });
}
if (!Breadcrumbs::exists('service-desk.releases.create')) {
    Breadcrumbs::register('service-desk.releases.create', function($breadcrumbs) {
        $breadcrumbs->parent('service-desk.releases.index');
        $breadcrumbs->push(Lang::get('ServiceDesk::lang.create'), route('service-desk.releases.create'));
    });
}
if (!Breadcrumbs::exists('service-desk.releases.edit')) {

    Breadcrumbs::register('service-desk.releases.edit', function($breadcrumbs, $id) {
        $breadcrumbs->parent('service-desk.releases.index');
        $breadcrumbs->push(Lang::get('ServiceDesk::lang.edit'), route('service-desk.releases.edit', $id));
    });
}
if (!Breadcrumbs::exists('service-desk.releases.view')) {
    Breadcrumbs::register('service-desk.releases.view', function($breadcrumbs, $id) {
        $breadcrumbs->parent('service-desk.releases.index');
        $breadcrumbs->push(Lang::get('ServiceDesk::lang.view'), route('service-desk.releases.view', $id));
    });
}
if (!Breadcrumbs::exists('service-desk.changes.index')) {

    /**
     * Change
     */
    Breadcrumbs::register('service-desk.changes.index', function($breadcrumbs) {
        $breadcrumbs->parent('dashboard');
        $breadcrumbs->push(Lang::get('ServiceDesk::lang.change'), route('service-desk.changes.index'));
    });
}
if (!Breadcrumbs::exists('service-desk.changes.create')) {
    Breadcrumbs::register('service-desk.changes.create', function($breadcrumbs) {
        $breadcrumbs->parent('service-desk.changes.index');
        $breadcrumbs->push(Lang::get('ServiceDesk::lang.create'), route('service-desk.changes.create'));
    });
}
if (!Breadcrumbs::exists('service-desk.changes.edit')) {
    Breadcrumbs::register('service-desk.changes.edit', function($breadcrumbs, $id) {
        $breadcrumbs->parent('service-desk.changes.index');
        $breadcrumbs->push(Lang::get('ServiceDesk::lang.edit'), route('service-desk.changes.edit', $id));
    });
}
if (!Breadcrumbs::exists('service-desk.changes.show')) {
    Breadcrumbs::register('service-desk.changes.show', function($breadcrumbs, $id) {
        $breadcrumbs->parent('service-desk.changes.index');
        $breadcrumbs->push(Lang::get('ServiceDesk::lang.view'), route('service-desk.changes.show', $id));
    });
}
/**
 * Problems
 */
if (!Breadcrumbs::exists('service-desk.problem.index')) {

    Breadcrumbs::register('service-desk.problem.index', function($breadcrumbs) {
        $breadcrumbs->parent('dashboard');
        $breadcrumbs->push(Lang::get('ServiceDesk::lang.problem'), route('service-desk.problem.index'));
    });
}
if (!Breadcrumbs::exists('service-desk.problem.create')) {
    Breadcrumbs::register('service-desk.problem.create', function($breadcrumbs) {
        $breadcrumbs->parent('service-desk.problem.index');
        $breadcrumbs->push(Lang::get('ServiceDesk::lang.create'), route('service-desk.problem.create'));
    });
}
if (!Breadcrumbs::exists('service-desk.problem.edit')) {
    Breadcrumbs::register('service-desk.problem.edit', function($breadcrumbs, $id) {
        $breadcrumbs->parent('service-desk.problem.index');
        $breadcrumbs->push(Lang::get('ServiceDesk::lang.edit'), route('service-desk.problem.edit', $id));
    });
}
if (!Breadcrumbs::exists('show.problem')) {
    Breadcrumbs::register('show.problem', function($breadcrumbs, $id) {
        $breadcrumbs->parent('service-desk.problem.index');
        $breadcrumbs->push(Lang::get('ServiceDesk::lang.view'), route('show.problem', $id));
    });
}

/**
 * Announcement
 */
if (!Breadcrumbs::exists('announcement')) {

    Breadcrumbs::register('announcement', function($breadcrumbs) {
        $breadcrumbs->parent('setting');
        $breadcrumbs->push(Lang::get('ServiceDesk::lang.announcement-page-title'), route('announcement'));
    });
}

Breadcrumbs::register('servicedesk.reports', function($breadcrumbs) {
    $lang = 'ServiceDesk::lang.servicedesk_reports';
    $breadcrumbs->push(Lang::get('ServiceDesk::lang.servicedesk_reports'), url("service-desk/reports"));
});

// breadcrumb for report list
Breadcrumbs::register('servicedesk.report.list', function($breadcrumbs, $reportType) {
    $breadcrumbs->parent('servicedesk.reports');
    $lang = 'ServiceDesk::lang.'.$reportType.'_reports';
    $breadcrumbs->push(Lang::get($lang), url("service-desk/reports/$reportType"));
});

// breadcrumb for report create
Breadcrumbs::register('servicedesk.report.create', function($breadcrumbs, $reportType) {
    $breadcrumbs->parent('servicedesk.report.list',$reportType);
    $lang = 'ServiceDesk::lang.'.$reportType.'_reports';
    $breadcrumbs->push(Lang::get('ServiceDesk::lang.create'));
});

// breadcrumb for report edit
Breadcrumbs::register('servicedesk.report.edit', function($breadcrumbs, $reportType) {
    $breadcrumbs->parent('servicedesk.report.list',$reportType);
    $lang = 'ServiceDesk::lang.'.$reportType.'_reports';
    $breadcrumbs->push(Lang::get('ServiceDesk::lang.edit'));
});

// breadcrumb for report view
Breadcrumbs::register('servicedesk.report.view', function($breadcrumbs, $reportType) {
    $breadcrumbs->parent('servicedesk.report.list',$reportType);
    $lang = 'ServiceDesk::lang.'.$reportType.'_report';
    $breadcrumbs->push(Lang::get('ServiceDesk::lang.view'));
});

// barcode 'servicedeskbarcode.settings'
Breadcrumbs::register('servicedesk.barcode.settings', function($breadcrumbs) {
    $breadcrumbs->parent('setting');
    $breadcrumbs->push(Lang::get('ServiceDesk::lang.barcode_settings'));
});

/**
 * Asset Status Breadcrumbs
 */
Breadcrumbs::register('service-desk.asset-status.index', function($breadcrumbs) {
    $breadcrumbs->parent('setting');
    $breadcrumbs->push(trans('ServiceDesk::lang.asset_status'), route('service-desk.asset-status.index'));
});

Breadcrumbs::register('service-desk.asset-status.create', function($breadcrumbs) {
    $breadcrumbs->parent('service-desk.asset-status.index');
    $breadcrumbs->push(trans('ServiceDesk::lang.create'), route('service-desk.asset-status.create'));
});


Breadcrumbs::register('service-desk.asset-status.edit', function($breadcrumbs, $assetStatusId) {
    $breadcrumbs->parent('service-desk.asset-status.index');
    $breadcrumbs->push(trans('ServiceDesk::lang.edit'), route('service-desk.asset-status.edit', $assetStatusId));
});



    