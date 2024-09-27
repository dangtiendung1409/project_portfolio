<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use File;

class createViewFileAddForm extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:adminAddFormView {view}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'create a new view file';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $viewname = $this->argument('view');

        $viewname = $viewname.'.blade.php';

        $pathname = "resources/views/{$viewname}";

        if(File::exists($pathname)){
            $this->error("file {$pathname} is already exist " );
            return;
        }
        $dir = dirname($pathname);
        if(!file_exists($dir))
        {
            mkdir($dir,0777,true);
        }

        $content = '@extends("admin/layout")
@section("content")
    <section class="is-title-bar">
        <div class="flex flex-col md:flex-row items-center justify-between space-y-6 md:space-y-0">
            <ul>
                <li>Admin</li>
                <li>Forms</li>
            </ul>
            <a href="https://justboil.me/" target="_blank" class="button blue">
                <span class="icon"><i class="mdi mdi-credit-card-outline"></i></span>
                <span>Premium Demo</span>
            </a>
        </div>
    </section>

    <section class="is-hero-bar">
        <div class="flex flex-col md:flex-row items-center justify-between space-y-6 md:space-y-0">
            <h1 class="title">
                Forms
            </h1>
            <button class="button light">Button</button>
        </div>
    </section>

    <section class="section main-section">
        <div class="card mb-6">
            <header class="card-header">
                <p class="card-header-title">
                    <span class="icon"><i class="mdi mdi-ballot"></i></span>
                    Forms
                </p>
            </header>
            <div class="card-content">
                <form method="get">
                    <div class="field">
                        <label class="label">From</label>
                        <div class="field-body">
                            <div class="field">
                                <div class="control icons-left">
                                    <input class="input" type="text" placeholder="Name">
                                    <span class="icon left"><i class="mdi mdi-account"></i></span>
                                </div>
                            </div>
                            <div class="field">
                                <div class="control icons-left icons-right">
                                    <input class="input" type="email" placeholder="Email" value="alex@smith.com">
                                    <span class="icon left"><i class="mdi mdi-mail"></i></span>
                                    <span class="icon right"><i class="mdi mdi-check"></i></span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="field">
                        <div class="field-body">
                            <div class="field">
                                <div class="field addons">
                                    <div class="control">
                                        <input class="input" value="+44" size="3" readonly>
                                    </div>
                                    <div class="control expanded">
                                        <input class="input" type="tel" placeholder="Your phone number">
                                    </div>
                                </div>
                                <p class="help">Do not enter the first zero</p>
                            </div>
                        </div>
                    </div>
                    <div class="field">
                        <label class="label">Department</label>
                        <div class="control">
                            <div class="select">
                                <select>
                                    <option>Business development</option>
                                    <option>Marketing</option>
                                    <option>Sales</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <hr>
                    <div class="field">
                        <label class="label">Subject</label>

                        <div class="control">
                            <input class="input" type="text" placeholder="e.g. Partnership opportunity">
                        </div>
                        <p class="help">
                            This field is required
                        </p>
                    </div>

                    <div class="field">
                        <label class="label">Question</label>
                        <div class="control">
                            <textarea class="textarea" placeholder="Explain how we can help you"></textarea>
                        </div>
                    </div>
                    <hr>

                    <div class="field grouped">
                        <div class="control">
                            <button type="submit" class="button green">
                                Submit
                            </button>
                        </div>
                        <div class="control">
                            <button type="reset" class="button red">
                                Reset
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <div class="card">
            <header class="card-header">
                <p class="card-header-title">
                    <span class="icon"><i class="mdi mdi-ballot-outline"></i></span>
                    Custom elements
                </p>
            </header>
            <div class="card-content">
                <div class="field">
                    <label class="label">Checkbox</label>
                    <div class="field-body">
                        <div class="field grouped multiline">
                            <div class="control">
                                <label class="checkbox"><input type="checkbox" value="lorem" checked>
                                    <span class="check"></span>
                                    <span class="control-label">Lorem</span>
                                </label>
                            </div>
                            <div class="control">
                                <label class="checkbox"><input type="checkbox" value="ipsum">
                                    <span class="check"></span>
                                    <span class="control-label">Ipsum</span>
                                </label>
                            </div>
                            <div class="control">
                                <label class="checkbox"><input type="checkbox" value="dolore">
                                    <span class="check is-primary"></span>
                                    <span class="control-label">Dolore</span>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
                <hr>
                <div class="field">
                    <label class="label">Radio</label>
                    <div class="field-body">
                        <div class="field grouped multiline">
                            <div class="control">
                                <label class="radio">
                                    <input type="radio" name="sample-radio" value="one" checked>
                                    <span class="check"></span>
                                    <span class="control-label">One</span>
                                </label>
                            </div>
                            <div class="control">
                                <label class="radio">
                                    <input type="radio" name="sample-radio" value="two">
                                    <span class="check"></span>
                                    <span class="control-label">Two</span>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
                <hr>
                <div class="field">
                    <label class="label">Switch</label>
                    <div class="field-body">
                        <div class="field">
                            <label class="switch">
                                <input type="checkbox" value="false">
                                <span class="check"></span>
                                <span class="control-label">Default</span>
                            </label>
                        </div>
                    </div>
                </div>
                <hr>
                <div class="field">
                    <label class="label">File</label>
                    <div class="field-body">
                        <div class="field file">
                            <label class="upload control">
                                <a class="button blue">
                                    Upload
                                </a>
                                <input type="file">
                            </label>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
';

        File::put($pathname , $content);

        $this->info("File {$pathname} is created");

    }
}
