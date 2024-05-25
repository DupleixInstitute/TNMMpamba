<?php

namespace App\Console\Commands;

use App\Models\LoanApplication;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class FixScript extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:fix-script';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This is fixing the script, modified depending on the changes made in the database';

    /**
     * Execute the console command.
     */
    public function handle()
    {
       $applications = LoanApplication::get();
       foreach ($applications as $application) {
        //    dd($application);
           //check related approval stages for that loan application
           $linkedStages = $application->linkedStages;
           //check the stage with is_current_stage = 1
              $currentStage = $linkedStages->where('is_current', 1)->first();
                if($currentStage){
                    if($application->current_loan_application_approval_stage_id != $currentStage->id){
                        Log::info('Loan application with id '.$application->id.' has a mismatch in the current stage');
                        //update it
                        $application->update([
                            'current_loan_application_approval_stage_id' => $currentStage->id
                        ]);
                     }

                }
            //check the loan current stage column in the loan application table

          //check the current stage

       }
    }
}
