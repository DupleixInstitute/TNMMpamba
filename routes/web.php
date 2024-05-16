<?php

use Webit\Util\EvalMath\EvalMath;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BanksController;
use App\Http\Controllers\FilesController;
use App\Http\Controllers\FormsController;
use App\Http\Controllers\RolesController;
use App\Http\Controllers\UsersController;
use App\Http\Controllers\WardsController;
use App\Http\Controllers\ClientsController;
use App\Http\Controllers\CoursesController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\LicenseController;
use App\Http\Controllers\RegionsController;
use App\Http\Controllers\ReportsController;
use App\Http\Controllers\TaxRateController;
use App\Http\Controllers\BranchesController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\VillagesController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DistrictsController;
use App\Http\Controllers\InkhundlaController;
use App\Http\Controllers\LoanFilesController;
use App\Http\Controllers\LoanNotesController;
use App\Http\Controllers\ProvincesController;
use App\Http\Controllers\CurrenciesController;
use App\Http\Controllers\LegalTypesController;
use App\Http\Controllers\ClientFilesController;
use App\Http\Controllers\ClientNotesController;
use App\Http\Controllers\ExcelExportController;
use App\Http\Controllers\ExpenseTypeController;
use App\Http\Controllers\PaymentTypeController;
use App\Http\Controllers\SmsGatewaysController;
use App\Http\Controllers\ClientIncomeController;
use App\Http\Controllers\ClientPotersController;
use App\Http\Controllers\JournalEntryController;
use App\Http\Controllers\LoanProductsController;
use App\Http\Controllers\CommunicationController;
use App\Http\Controllers\IndustryTypesController;
use App\Http\Controllers\ChartOfAccountController;
use App\Http\Controllers\LoanGuarantorsController;
use App\Http\Controllers\CourseMaterialsController;
use App\Http\Controllers\EventCategoriesController;
use App\Http\Controllers\FinancialPeriodController;

use App\Http\Controllers\CommunicationLogController;
use App\Http\Controllers\CourseCategoriesController;
use App\Http\Controllers\LoanApplicationsController;
use App\Http\Controllers\ArticleCategoriesController;
use App\Http\Controllers\ScoringAttributesController;
use App\Http\Controllers\ClientBalanceSheetController;
use App\Http\Controllers\ClientLoginDetailsController;
use App\Http\Controllers\ClientShareholdersController;
use App\Http\Controllers\LoanApprovalStagesController;
use App\Http\Controllers\ClientRatioAnalysisController;
use App\Http\Controllers\CourseRegistrationsController;
use App\Http\Controllers\CommunicationCampaignController;
use App\Http\Controllers\CommunicationTemplateController;
use App\Http\Controllers\LoanProductCategoriesController;
use App\Http\Controllers\MemberPortal\MemberPortalLoansController;
use App\Http\Controllers\MemberPortal\MemberPortalUsersController;
use App\Http\Controllers\MemberPortal\MemberPortalEventsController;
use App\Http\Controllers\MemberPortal\MemberPortalCoursesController;
use App\Http\Controllers\MemberPortal\MemberPortalArticlesController;
use App\Http\Controllers\MemberPortal\MemberPortalLoanFilesController;
use App\Http\Controllers\MemberPortal\MemberPortalLoanNotesController;
use App\Http\Controllers\MemberPortal\MemberPortalLoanGuarantorsController;
use App\Http\Controllers\MemberPortal\MemberPortalCourseMaterialsController;
use App\Http\Controllers\MemberPortal\MemberPortalCourseRegistrationsController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
*/


Route::get('/', [DashboardController::class, 'index'])->name('home');
Route::post('/export/excel', [DashboardController::class, 'export'])->name('export.excel');
Route::group(['prefix' => 'dashboard'], function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('{scope}/create-filter', [DashboardController::class, 'filter'])->name('dashboard.filter');
    Route::get('filter-results', [DashboardController::class, 'filterResults'])->name('dashboard.filter-results');
});
//users
Route::group(['prefix' => 'user'], function () {
    Route::get('/', [UsersController::class, 'index'])->name('users.index');
    Route::get('/search', [UsersController::class, 'search'])->name('users.search');
    Route::get('/create', [UsersController::class, 'create'])->name('users.create');
    Route::post('/store', [UsersController::class, 'store'])->name('users.store');
    Route::get('/{user}/show', [UsersController::class, 'show'])->name('users.show');
    Route::get('/{user}/edit', [UsersController::class, 'edit'])->name('users.edit');
    Route::put('/{user}/update', [UsersController::class, 'update'])->name('users.update');
    Route::delete('/{user}/destroy', [UsersController::class, 'destroy'])->name('users.destroy');
    //manage roles
    Route::get('/role', [RolesController::class, 'index'])->name('users.roles.index');
    Route::get('/role/create', [RolesController::class, 'create'])->name('users.roles.create');
    Route::post('/role/store', [RolesController::class, 'store'])->name('users.roles.store');
    Route::get('/role/{role}/show', [RolesController::class, 'show'])->name('users.roles.show');
    Route::get('/role/{role}/edit', [RolesController::class, 'edit'])->name('users.roles.edit');
    Route::put('/role/{role}/update', [RolesController::class, 'update'])->name('users.roles.update');
    Route::delete('/role/{role}/destroy', [RolesController::class, 'destroy'])->name('users.roles.destroy');

});
//loan products
Route::prefix('loan_product')->name('loan_products.')->group(function () {
    Route::get('/', [LoanProductsController::class, 'index'])->name('index');
    Route::get('/create', [LoanProductsController::class, 'create'])->name('create');
    Route::post('/store', [LoanProductsController::class, 'store'])->name('store');
    Route::get('/{product}/show', [LoanProductsController::class, 'show'])->name('show');
    Route::get('/{product}/edit', [LoanProductsController::class, 'edit'])->name('edit');
    Route::put('/{product}/update', [LoanProductsController::class, 'update'])->name('update');
    Route::delete('/{product}/destroy', [LoanProductsController::class, 'destroy'])->name('destroy');
    Route::post('/{product}/sync_attributes', [LoanProductsController::class, 'syncAttributes'])->name('sync_attributes');
//comments
    Route::post('/{article}/comment/store', [LoanProductsController::class, 'storeComment'])->name('comments.store');
    Route::delete('/comment/{comment}/destroy', [LoanProductsController::class, 'destroyComment'])->name('comments.destroy');

    //categories
    Route::prefix('category')->name('categories.')->group(function () {
        Route::get('/', [LoanProductCategoriesController::class, 'index'])->name('index');
        Route::get('/create', [LoanProductCategoriesController::class, 'create'])->name('create');
        Route::post('/store', [LoanProductCategoriesController::class, 'store'])->name('store');
        Route::get('/{category}/edit', [LoanProductCategoriesController::class, 'edit'])->name('edit');
        Route::put('/{category}/update', [LoanProductCategoriesController::class, 'update'])->name('update');
        Route::delete('/{category}/destroy', [LoanProductCategoriesController::class, 'destroy'])->name('destroy');
    });
});
//events
Route::prefix('scoring_attribute')->name('scoring_attributes.')->group(function () {
    Route::get('/', [ScoringAttributesController::class, 'index'])->name('index');
    Route::get('/create', [ScoringAttributesController::class, 'create'])->name('create');
    Route::post('/store', [ScoringAttributesController::class, 'store'])->name('store');
    Route::get('/{attribute}/show', [ScoringAttributesController::class, 'show'])->name('show');
    Route::get('/{attribute}/edit', [ScoringAttributesController::class, 'edit'])->name('edit');
    Route::put('/{attribute}/update', [ScoringAttributesController::class, 'update'])->name('update');
    Route::delete('/{attribute}/destroy', [ScoringAttributesController::class, 'destroy'])->name('destroy');
    Route::post('/{attribute}/items/store', [ScoringAttributesController::class, 'storeItem'])->name('items.store');
    Route::delete('/items/{attribute}/destroy', [ScoringAttributesController::class, 'destroyItem'])->name('items.destroy');

});

//clients
Route::group(['prefix' => 'client', 'as' => 'clients.'], function () {
    Route::get('/', [ClientsController::class, 'index'])->name('index');
    Route::get('/create', [ClientsController::class, 'create'])->name('create');
    Route::get('/search', [ClientsController::class, 'search'])->name('search');
    Route::post('/store', [ClientsController::class, 'store'])->name('store');
    Route::get('/{client}/show', [ClientsController::class, 'show'])->name('show');
    Route::get('/{client}/edit', [ClientsController::class, 'edit'])->name('edit');
    Route::put('/{client}/update', [ClientsController::class, 'update'])->name('update');
    Route::delete('/{client}/destroy', [ClientsController::class, 'destroy'])->name('destroy');
    //loans
    Route::get('{client}/loan_application', [ClientsController::class, 'loanApplication'])->name('loan_applications.index');
    Route::get('{client}/course', [ClientsController::class, 'courses'])->name('courses.index');

    //login details
    Route::get('{client}/login_detail', [ClientLoginDetailsController::class, 'index'])->name('login_details.index');
    Route::get('{client}/login_detail/create', [ClientLoginDetailsController::class, 'create'])->name('login_details.create');
    Route::post('{client}/login_detail/store', [ClientLoginDetailsController::class, 'store'])->name('login_details.store');
    Route::delete('login_detail/{clientUser}/destroy', [ClientLoginDetailsController::class, 'destroy'])->name('login_details.destroy');
    //files
    Route::get('{client}/file', [ClientFilesController::class, 'index'])->name('files.index');
    Route::get('{client}/file/create', [ClientFilesController::class, 'create'])->name('files.create');
    Route::post('{client}/file/store', [ClientFilesController::class, 'store'])->name('files.store');
    Route::get('file/{file}/show', [ClientFilesController::class, 'show'])->name('files.show');
    Route::get('file/{file}/edit', [ClientFilesController::class, 'edit'])->name('files.edit');
    Route::put('file/{file}/update', [ClientFilesController::class, 'update'])->name('files.update');
    Route::delete('file/{file}/destroy', [ClientFilesController::class, 'destroy'])->name('files.destroy');
    //ratio analysis
    Route::get('{client}/ratio_analysis', [ClientRatioAnalysisController::class, 'index'])->name('ratio_analysis.index');
    Route::get('{client}/ratio_analysis/create', [ClientRatioAnalysisController::class, 'create'])->name('ratio_analysis.create');
    Route::post('{client}/ratio_analysis/store', [ClientRatioAnalysisController::class, 'store'])->name('ratio_analysis.store');
    Route::get('ratio_analysis/{ratio}/show', [ClientRatioAnalysisController::class, 'show'])->name('ratio_analysis.show');
    Route::get('ratio_analysis/{ratio}/edit', [ClientRatioAnalysisController::class, 'edit'])->name('ratio_analysis.edit');
    Route::put('ratio_analysis/{ratio}/update', [ClientRatioAnalysisController::class, 'update'])->name('ratio_analysis.update');
    Route::delete('ratio_analysis/{ratio}/destroy', [ClientRatioAnalysisController::class, 'destroy'])->name('ratio_analysis.destroy');
    //notes
    Route::get('{client}/note', [ClientNotesController::class, 'index'])->name('notes.index');
    Route::get('{client}/note/create', [ClientNotesController::class, 'create'])->name('notes.create');
    Route::post('{client}/note/store', [ClientNotesController::class, 'store'])->name('notes.store');
    Route::get('note/{clientNote}/show', [ClientNotesController::class, 'show'])->name('notes.show');
    Route::get('note/{clientNote}/edit', [ClientNotesController::class, 'edit'])->name('notes.edit');
    Route::put('note/{clientNote}/update', [ClientNotesController::class, 'update'])->name('notes.update');
    Route::delete('note/{clientNote}/destroy', [ClientNotesController::class, 'destroy'])->name('notes.destroy');
    //shareholders
    Route::get('{client}/shareholder', [ClientShareholdersController::class, 'index'])->name('shareholders.index');
    Route::get('{client}/shareholder/create', [ClientShareholdersController::class, 'create'])->name('shareholders.create');
    Route::post('{client}/shareholder/store', [ClientShareholdersController::class, 'store'])->name('shareholders.store');
    Route::get('shareholder/{shareholder}/show', [ClientShareholdersController::class, 'show'])->name('shareholders.show');
    Route::get('shareholder/{shareholder}/edit', [ClientShareholdersController::class, 'edit'])->name('shareholders.edit');
    Route::put('shareholder/{shareholder}/update', [ClientShareholdersController::class, 'update'])->name('shareholders.update');
    Route::delete('shareholder/{shareholder}/destroy', [ClientShareholdersController::class, 'destroy'])->name('shareholders.destroy');
    //balance sheet
    Route::get('{client}/balance_sheet', [ClientBalanceSheetController::class, 'index'])->name('balance_sheets.index');
    Route::get('{client}/balance_sheet/summary', [ClientBalanceSheetController::class, 'summary'])->name('balance_sheets.summary');
    Route::get('{client}/balance_sheet/create', [ClientBalanceSheetController::class, 'create'])->name('balance_sheets.create');
    Route::post('{client}/balance_sheet/store', [ClientBalanceSheetController::class, 'store'])->name('balance_sheets.store');
    Route::get('balance_sheet/{sheet}/show', [ClientBalanceSheetController::class, 'show'])->name('balance_sheets.show');
    Route::get('balance_sheet/{sheet}/edit', [ClientBalanceSheetController::class, 'edit'])->name('balance_sheets.edit');
    Route::put('balance_sheet/{sheet}/update', [ClientBalanceSheetController::class, 'update'])->name('balance_sheets.update');
    Route::delete('balance_sheet/{sheet}/destroy', [ClientBalanceSheetController::class, 'destroy'])->name('balance_sheets.destroy');
    //income statement
    Route::get('{client}/income_statement', [ClientIncomeController::class, 'index'])->name('income_statements.index');
    Route::get('{client}/income_statement/create', [ClientIncomeController::class, 'create'])->name('income_statements.create');
    Route::post('{client}/income_statement/store', [ClientIncomeController::class, 'store'])->name('income_statements.store');
    Route::get('income_statement/{statement}/show', [ClientIncomeController::class, 'show'])->name('income_statements.show');
    Route::get('income_statement/{statement}/edit', [ClientIncomeController::class, 'edit'])->name('income_statements.edit');
    Route::put('income_statement/{statement}/update', [ClientIncomeController::class, 'update'])->name('income_statements.update');
    Route::delete('income_statement/{statement}/destroy', [ClientIncomeController::class, 'destroy'])->name('income_statements.destroy');
    //poter
    Route::get('{client}/porter', [ClientPotersController::class, 'index'])->name('poter.index');
    Route::get('{client}/porter/create', [ClientPotersController::class, 'create'])->name('poter.create');
    Route::post('{client}/porter/store', [ClientPotersController::class, 'store'])->name('poter.store');
    Route::get('porter/{poter}/show', [ClientPotersController::class, 'show'])->name('poter.show');
    Route::get('porter/{poter}/edit', [ClientPotersController::class, 'edit'])->name('poter.edit');
    Route::put('porter/{poter}/update', [ClientPotersController::class, 'update'])->name('poter.update');
    Route::delete('porter/{poter}/destroy', [ClientPotersController::class, 'destroy'])->name('poter.destroy');

});

//loan applications
Route::group(['prefix' => 'loan_application', 'as' => 'loan_applications.'], function () {
    Route::get('/', [LoanApplicationsController::class, 'index'])->name('index');
    Route::get('/create', [LoanApplicationsController::class, 'create'])->name('create');
    Route::post('/store', [LoanApplicationsController::class, 'store'])->name('store');
    Route::post('/{application}/change_status', [LoanApplicationsController::class, 'changeStatus'])->name('change_status');
    Route::post('/{application}/assign_approver', [LoanApplicationsController::class, 'assignApprover'])->name('assign_approver');
    Route::get('/{application}/show', [LoanApplicationsController::class, 'show'])->name('show');
    Route::get('/{application}/edit', [LoanApplicationsController::class, 'edit'])->name('edit');
    Route::put('/{application}/update', [LoanApplicationsController::class, 'update'])->name('update');
    Route::delete('/{application}/destroy', [LoanApplicationsController::class, 'destroy'])->name('destroy');
    Route::get('/{application}/comments', [LoanApplicationsController::class, 'showComments'])->name('show_comments');
    //files
    Route::get('{loan}/file', [LoanFilesController::class, 'index'])->name('files.index');
    Route::get('{loan}/file/create', [LoanFilesController::class, 'create'])->name('files.create');
    Route::post('{loan}/file/store', [LoanFilesController::class, 'store'])->name('files.store');
    Route::get('file/{file}/show', [LoanFilesController::class, 'show'])->name('files.show');
    Route::get('file/{file}/edit', [LoanFilesController::class, 'edit'])->name('files.edit');
    Route::put('file/{file}/update', [LoanFilesController::class, 'update'])->name('files.update');
    Route::delete('file/{file}/destroy', [LoanFilesController::class, 'destroy'])->name('files.destroy');
    //notes
    Route::get('{loan}/note', [LoanNotesController::class, 'index'])->name('notes.index');
    Route::get('{loan}/note/create', [LoanNotesController::class, 'create'])->name('notes.create');
    Route::post('{loan}/note/store', [LoanNotesController::class, 'store'])->name('notes.store');
    Route::get('note/{note}/show', [LoanNotesController::class, 'show'])->name('notes.show');
    Route::get('note/{note}/edit', [LoanNotesController::class, 'edit'])->name('notes.edit');
    Route::put('note/{note}/update', [LoanNotesController::class, 'update'])->name('notes.update');
    Route::delete('note/{note}/destroy', [LoanNotesController::class, 'destroy'])->name('notes.destroy');
    //guarantors
    Route::get('{loan}/guarantor', [LoanGuarantorsController::class, 'index'])->name('guarantors.index');
    Route::get('{loan}/guarantor/create', [LoanGuarantorsController::class, 'create'])->name('guarantors.create');
    Route::post('{loan}/guarantor/store', [LoanGuarantorsController::class, 'store'])->name('guarantors.store');
    Route::get('guarantor/{guarantor}/show', [LoanGuarantorsController::class, 'show'])->name('guarantors.show');
    Route::get('guarantor/{guarantor}/edit', [LoanGuarantorsController::class, 'edit'])->name('guarantors.edit');
    Route::put('guarantor/{guarantor}/update', [LoanGuarantorsController::class, 'update'])->name('guarantors.update');
    Route::delete('guarantor/{guarantor}/destroy', [LoanGuarantorsController::class, 'destroy'])->name('guarantors.destroy');

    Route::prefix('category')->name('categories.')->group(function () {
        Route::get('/', [LoanProductCategoriesController::class, 'index'])->name('index');
        Route::get('/create', [LoanProductCategoriesController::class, 'create'])->name('create');
        Route::post('/store', [LoanProductCategoriesController::class, 'store'])->name('store');
        Route::get('/{category}/edit', [LoanProductCategoriesController::class, 'edit'])->name('edit');
        Route::put('/{category}/update', [LoanProductCategoriesController::class, 'update'])->name('update');
        Route::delete('/{category}/destroy', [LoanProductCategoriesController::class, 'destroy'])->name('destroy');
    });
});

//files
Route::group(['prefix' => 'file'], function () {
    Route::get('/', [FilesController::class, 'index'])->name('files.index');
    Route::post('/upload', [FilesController::class, 'upload'])->name('files.upload');
    Route::get('/{file}/download', [FilesController::class, 'download'])->name('files.download');
    Route::get('/create', [FilesController::class, 'create'])->name('files.create');
    Route::post('/store', [FilesController::class, 'store'])->name('files.store');
    Route::get('/{id}/show', [FilesController::class, 'show'])->name('files.show');
    Route::get('/{id}/edit', [FilesController::class, 'edit'])->name('files.edit');
    Route::put('/{id}/update', [FilesController::class, 'update'])->name('files.update');
    Route::delete('/{id}/destroy', [FilesController::class, 'destroy'])->name('files.destroy');
});
//branches
Route::group(['prefix' => 'branch'], function () {
    Route::get('/', [BranchesController::class, 'index'])->name('branches.index');
    Route::get('/create', [BranchesController::class, 'create'])->name('branches.create');
    Route::post('/store', [BranchesController::class, 'store'])->name('branches.store');
    Route::get('/{branch}/show', [BranchesController::class, 'show'])->name('branches.show');
    Route::get('/{branch}/edit', [BranchesController::class, 'edit'])->name('branches.edit');
    Route::put('/{branch}/update', [BranchesController::class, 'update'])->name('branches.update');
    Route::delete('/{branch}/destroy', [BranchesController::class, 'destroy'])->name('branches.destroy');
});
//industry_types
Route::group(['prefix' => 'industry_type'], function () {
    Route::get('/', [IndustryTypesController::class, 'index'])->name('industry_types.index');
    Route::get('/create', [IndustryTypesController::class, 'create'])->name('industry_types.create');
    Route::post('/store', [IndustryTypesController::class, 'store'])->name('industry_types.store');
    Route::get('/{type}/show', [IndustryTypesController::class, 'show'])->name('industry_types.show');
    Route::get('/{type}/edit', [IndustryTypesController::class, 'edit'])->name('industry_types.edit');
    Route::put('/{type}/update', [IndustryTypesController::class, 'update'])->name('industry_types.update');
    Route::delete('/{type}/destroy', [IndustryTypesController::class, 'destroy'])->name('industry_types.destroy');
});
//legal_types
Route::group(['prefix' => 'legal_type'], function () {
    Route::get('/', [LegalTypesController::class, 'index'])->name('legal_types.index');
    Route::get('/create', [LegalTypesController::class, 'create'])->name('legal_types.create');
    Route::post('/store', [LegalTypesController::class, 'store'])->name('legal_types.store');
    Route::get('/{type}/show', [LegalTypesController::class, 'show'])->name('legal_types.show');
    Route::get('/{type}/edit', [LegalTypesController::class, 'edit'])->name('legal_types.edit');
    Route::put('/{type}/update', [LegalTypesController::class, 'update'])->name('legal_types.update');
    Route::delete('/{type}/destroy', [LegalTypesController::class, 'destroy'])->name('legal_types.destroy');
});
//chart_of_accounts
Route::group(['prefix' => 'chart_of_account'], function () {
    Route::get('/', [ChartOfAccountController::class, 'index'])->name('chart_of_accounts.index');
    Route::get('/create', [ChartOfAccountController::class, 'create'])->name('chart_of_accounts.create');
    Route::post('/store', [ChartOfAccountController::class, 'store'])->name('chart_of_accounts.store');
    Route::get('/{chartOfAccount}/show', [ChartOfAccountController::class, 'show'])->name('chart_of_accounts.show');
    Route::get('/{chartOfAccount}/edit', [ChartOfAccountController::class, 'edit'])->name('chart_of_accounts.edit');
    Route::put('/{chartOfAccount}/update', [ChartOfAccountController::class, 'update'])->name('chart_of_accounts.update');
    Route::delete('/{chartOfAccount}/destroy', [ChartOfAccountController::class, 'destroy'])->name('chart_of_accounts.destroy');
});
//banks
Route::group(['prefix' => 'bank'], function () {
    Route::get('/', [BanksController::class, 'index'])->name('banks.index');
    Route::get('/create', [BanksController::class, 'create'])->name('banks.create');
    Route::post('/store', [BanksController::class, 'store'])->name('banks.store');
    Route::get('/{bank}/show', [BanksController::class, 'show'])->name('banks.show');
    Route::get('/{bank}/edit', [BanksController::class, 'edit'])->name('banks.edit');
    Route::put('/{bank}/update', [BanksController::class, 'update'])->name('banks.update');
    Route::delete('/{bank}/destroy', [BanksController::class, 'destroy'])->name('banks.destroy');
});
//locations
Route::group(['prefix' => 'location', 'as' => 'locations.'], function () {
    //regions for swaziland
    Route::group(['prefix' => 'region'], function () {
        Route::get('/', [RegionsController::class, 'index'])->name('regions.index');
        Route::get('/create', [RegionsController::class, 'create'])->name('regions.create');
        Route::post('/store', [RegionsController::class, 'store'])->name('regions.store');
        Route::get('/{province}/show', [RegionsController::class, 'show'])->name('regions.show');
        Route::get('/{province}/edit', [RegionsController::class, 'edit'])->name('regions.edit');
        Route::put('/{province}/update', [RegionsController::class, 'update'])->name('regions.update');
        Route::delete('/{province}/destroy', [RegionsController::class, 'destroy'])->name('regions.destroy');
    });
    Route::group(['prefix' => 'province'], function () {
        Route::get('/', [ProvincesController::class, 'index'])->name('provinces.index');
        Route::get('/create', [ProvincesController::class, 'create'])->name('provinces.create');
        Route::post('/store', [ProvincesController::class, 'store'])->name('provinces.store');
        Route::get('/{province}/show', [ProvincesController::class, 'show'])->name('provinces.show');
        Route::get('/{province}/edit', [ProvincesController::class, 'edit'])->name('provinces.edit');
        Route::put('/{province}/update', [ProvincesController::class, 'update'])->name('provinces.update');
        Route::delete('/{province}/destroy', [ProvincesController::class, 'destroy'])->name('provinces.destroy');
    });
    //inkhundla
    Route::group(['prefix' => 'inkhundla'], function () {
        Route::get('/', [InkhundlaController::class, 'index'])->name('inkhundla.index');
        Route::get('/create', [InkhundlaController::class, 'create'])->name('inkhundla.create');
        Route::post('/store', [InkhundlaController::class, 'store'])->name('inkhundla.store');
        Route::get('/{district}/show', [InkhundlaController::class, 'show'])->name('inkhundla.show');
        Route::get('/{district}/edit', [InkhundlaController::class, 'edit'])->name('inkhundla.edit');
        Route::put('/{district}/update', [InkhundlaController::class, 'update'])->name('inkhundla.update');
        Route::delete('/{district}/destroy', [InkhundlaController::class, 'destroy'])->name('inkhundla.destroy');
    });
    //districts
    Route::group(['prefix' => 'district'], function () {
        Route::get('/', [DistrictsController::class, 'index'])->name('districts.index');
        Route::get('/create', [DistrictsController::class, 'create'])->name('districts.create');
        Route::post('/store', [DistrictsController::class, 'store'])->name('districts.store');
        Route::get('/{district}/show', [DistrictsController::class, 'show'])->name('districts.show');
        Route::get('/{district}/edit', [DistrictsController::class, 'edit'])->name('districts.edit');
        Route::put('/{district}/update', [DistrictsController::class, 'update'])->name('districts.update');
        Route::delete('/{district}/destroy', [DistrictsController::class, 'destroy'])->name('districts.destroy');
    });
    //wards
    Route::group(['prefix' => 'ward'], function () {
        Route::get('/', [WardsController::class, 'index'])->name('wards.index');
        Route::get('/create', [WardsController::class, 'create'])->name('wards.create');
        Route::post('/store', [WardsController::class, 'store'])->name('wards.store');
        Route::get('/{ward}/show', [WardsController::class, 'show'])->name('wards.show');
        Route::get('/{ward}/edit', [WardsController::class, 'edit'])->name('wards.edit');
        Route::put('/{ward}/update', [WardsController::class, 'update'])->name('wards.update');
        Route::delete('/{ward}/destroy', [WardsController::class, 'destroy'])->name('wards.destroy');
    });
    //villages
    Route::group(['prefix' => 'village'], function () {
        Route::get('/', [VillagesController::class, 'index'])->name('villages.index');
        Route::get('/create', [VillagesController::class, 'create'])->name('villages.create');
        Route::post('/store', [VillagesController::class, 'store'])->name('villages.store');
        Route::get('/{village}/show', [VillagesController::class, 'show'])->name('villages.show');
        Route::get('/{village}/edit', [VillagesController::class, 'edit'])->name('villages.edit');
        Route::put('/{village}/update', [VillagesController::class, 'update'])->name('villages.update');
        Route::delete('/{village}/destroy', [VillagesController::class, 'destroy'])->name('villages.destroy');
    });
});
//currencies
Route::group(['prefix' => 'currency'], function () {
    Route::get('/', [CurrenciesController::class, 'index'])->name('currencies.index');
    Route::get('/create', [CurrenciesController::class, 'create'])->name('currencies.create');
    Route::post('/store', [CurrenciesController::class, 'store'])->name('currencies.store');
    Route::get('/{currency}/show', [CurrenciesController::class, 'show'])->name('currencies.show');
    Route::get('/{currency}/edit', [CurrenciesController::class, 'edit'])->name('currencies.edit');
    Route::put('/{currency}/update', [CurrenciesController::class, 'update'])->name('currencies.update');
    Route::delete('/{currency}/destroy', [CurrenciesController::class, 'destroy'])->name('currencies.destroy');
});
//approval stages
Route::group(['prefix' => 'loan_approval_stage'], function () {
    Route::get('/', [LoanApprovalStagesController::class, 'index'])->name('loan_approval_stages.index');
    Route::get('/create', [LoanApprovalStagesController::class, 'create'])->name('loan_approval_stages.create');
    Route::post('/store', [LoanApprovalStagesController::class, 'store'])->name('loan_approval_stages.store');
    Route::get('/{stage}/show', [LoanApprovalStagesController::class, 'show'])->name('loan_approval_stages.show');
    Route::get('/{stage}/edit', [LoanApprovalStagesController::class, 'edit'])->name('loan_approval_stages.edit');
    Route::put('/{stage}/update', [LoanApprovalStagesController::class, 'update'])->name('loan_approval_stages.update');
    Route::delete('/{stage}/destroy', [LoanApprovalStagesController::class, 'destroy'])->name('loan_approval_stages.destroy');
});

Route::group(['prefix' => 'financial_period'], function () {
    Route::get('/', [FinancialPeriodController::class, 'index'])->name('accounting.financial_periods.index');
    Route::get('/create', [FinancialPeriodController::class, 'create'])->name('accounting.financial_periods.create');
    Route::post('/store', [FinancialPeriodController::class, 'store'])->name('accounting.financial_periods.store');
    Route::get('/{financialPeriod}/show', [FinancialPeriodController::class, 'show'])->name('accounting.financial_periods.show');
    Route::get('/{financialPeriod}/edit', [FinancialPeriodController::class, 'edit'])->name('accounting.financial_periods.edit');
    Route::put('/{financialPeriod}/update', [FinancialPeriodController::class, 'update'])->name('accounting.financial_periods.update');
    Route::put('/{financialPeriod}/close', [FinancialPeriodController::class, 'close'])->name('accounting.financial_periods.close');
    Route::delete('/{financialPeriod}/destroy', [FinancialPeriodController::class, 'destroy'])->name('accounting.financial_periods.destroy');
});
//communication
Route::prefix('communication')->group(function () {
    Route::get('/', [CommunicationController::class, 'index']);
    //sms gateway
    Route::group(['prefix' => 'sms_gateway'], function () {
        Route::get('/', [SmsGatewaysController::class, 'index'])->name('communication.sms_gateways.index');
        Route::get('/create', [SmsGatewaysController::class, 'create'])->name('communication.sms_gateways.create');
        Route::post('/store', [SmsGatewaysController::class, 'store'])->name('communication.sms_gateways.store');
        Route::get('/{smsGateway}/show', [SmsGatewaysController::class, 'show'])->name('communication.sms_gateways.show');
        Route::get('/{smsGateway}/edit', [SmsGatewaysController::class, 'edit'])->name('communication.sms_gateways.edit');
        Route::put('/{smsGateway}/update', [SmsGatewaysController::class, 'update'])->name('communication.sms_gateways.update');
        Route::delete('/{smsGateway}/destroy', [SmsGatewaysController::class, 'destroy'])->name('communication.sms_gateways.destroy');
    });
    Route::prefix('campaign')->group(function () {
        Route::get('/', [CommunicationCampaignController::class, 'index'])->name('communication.campaigns.index');
        Route::get('/create', [CommunicationCampaignController::class, 'create'])->name('communication.campaigns.create');
        Route::post('/store', [CommunicationCampaignController::class, 'store'])->name('communication.campaigns.store');
        Route::get('/{communicationCampaign}/show', [CommunicationCampaignController::class, 'show'])->name('communication.campaigns.show');
        Route::get('/{communicationCampaign}/edit', [CommunicationCampaignController::class, 'edit'])->name('communication.campaigns.edit');
        Route::put('/{communicationCampaign}/update', [CommunicationCampaignController::class, 'update'])->name('communication.campaigns.update');
        Route::delete('/{communicationCampaign}/destroy', [CommunicationCampaignController::class, 'destroy'])->name('communication.campaigns.destroy');
    });
    Route::prefix('template')->group(function () {
        Route::get('/', [CommunicationTemplateController::class, 'index'])->name('communication.templates.index');
        Route::get('/create', [CommunicationTemplateController::class, 'create'])->name('communication.templates.create');
        Route::post('/store', [CommunicationTemplateController::class, 'store'])->name('communication.templates.store');
        Route::get('/{template}/show', [CommunicationTemplateController::class, 'show'])->name('communication.templates.show');
        Route::get('/{template}/edit', [CommunicationTemplateController::class, 'edit'])->name('communication.templates.edit');
        Route::put('/{template}/update', [CommunicationTemplateController::class, 'update'])->name('communication.templates.update');
        Route::delete('/{template}/destroy', [CommunicationTemplateController::class, 'destroy'])->name('communication.templates.destroy');
    });
    Route::prefix('log')->group(function () {
        Route::get('/', [CommunicationLogController::class, 'index'])->name('communication.logs.index');
        Route::get('{communicationCampaignLog}/destroy', [CommunicationLogController::class, 'destroy'])->name('communication.logs.destroy');
    });
});


//settings
Route::group(['prefix' => 'setting'], function () {
    Route::get('/', [SettingsController::class, 'index'])->name('settings.index');
    Route::get('/organisation', [SettingsController::class, 'organisation'])->name('settings.organisation');
    Route::get('/general', [SettingsController::class, 'general'])->name('settings.general');
    Route::post('/general/update', [SettingsController::class, 'generalUpdate'])->name('settings.general.update');
    Route::get('/system', [SettingsController::class, 'system'])->name('settings.system');
    Route::post('/system/update', [SettingsController::class, 'systemUpdate'])->name('settings.system.update');
    Route::get('/email', [SettingsController::class, 'email'])->name('settings.email');
    Route::post('/email/update', [SettingsController::class, 'emailUpdate'])->name('settings.email.update');
    Route::get('/sms', [SettingsController::class, 'sms'])->name('settings.sms');
    Route::post('/sms/update', [SettingsController::class, 'smsUpdate'])->name('settings.sms.update');
    Route::get('/other', [SettingsController::class, 'other'])->name('settings.other');
    Route::post('/other/update', [SettingsController::class, 'otherUpdate'])->name('settings.other.update');
    Route::get('/billing', [SettingsController::class, 'billing'])->name('settings.billing');
    Route::post('/update', [SettingsController::class, 'update'])->name('settings.update');
});
//reports
Route::group(['prefix' => 'report'], function () {
    Route::get('/', [ReportsController::class, 'index'])->name('reports.index');

});
//license
Route::group(['prefix' => 'license'], function () {
    Route::get('/', [LicenseController::class, 'index'])->name('license.index');
    Route::post('/verify', [LicenseController::class, 'verify'])->name('license.verify');

});


//client portal
Route::group(['prefix' => 'portal', 'as' => 'portal.'], function () {
    Route::get('/', [MemberPortalUsersController::class, 'dashboard'])->name('home');
    Route::get('/dashboard', [MemberPortalUsersController::class, 'dashboard'])->name('dashboard');
    Route::get('/search_clients', [MemberPortalUsersController::class, 'searchMembers'])->name('search_clients');
    //user
    Route::group(['prefix' => 'user'], function () {
        Route::get('profile', [MemberPortalUsersController::class, 'profile'])->name('profile.show');

    });

});
Route::get('test', function () {
    $total_tangible_net_worth = 0;
    $total_liabilities = null;
    if (!empty($total_tangible_net_worth)) {
        die('not empty test');
    } else {
        die('empty test yo');
    }
});
