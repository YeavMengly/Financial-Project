<div class="vertical-menu">
    <div data-simplebar class="h-100">
        <div id="sidebar-menu">
            <!-- Left Menu Start -->
            <ul class="metismenu list-unstyled" id="side-menu">
                <li class="{{ Request::routeIs('dashboard.*') ? 'mm-active' : '' }}">
                    <a href="{{ route('dashboard.index') }}"
                        class="{{ Request::routeIs('dashboard.*') ? 'active' : '' }}">
                        <i data-feather="home"></i>
                        <span data-key="t-dashboard">{{ __('menus.dashboard') }}</span>
                    </a>
                </li>
                {{-- Editing --}}
                <li>
                    <a href="javascript: void(0);" class="has-arrow">
                        <i data-feather="credit-card"></i>
                        <span data-key="t-beginning-credit">{{ __('menus.credit') }}</span>
                    </a>
                    <ul class="sub-menu" aria-expanded="false">
                        {{-- Start initial budget --}}
                        @if (hasPermission('ministries.index'))
                            <li class="{{ Request::routeIs('ministries.*') ? 'mm-active' : '' }}">
                                <a href="{{ route('ministries.index') }}"
                                    class="{{ Request::routeIs('ministries.*') ? 'active' : '' }}">
                                    <i data-feather="book"></i>
                                    <span data-key="t-ministries">{{ __('menus.ministries') }}</span>
                                </a>
                            </li>
                        @endif
                        {{-- End initial budget --}}

                        {{-- Start initial budget Mandate --}}
                        @if (hasPermission('initialBudgetMandate.index'))
                            <li class="{{ Request::routeIs('initialBudgetMandate.*') ? 'mm-active' : '' }}">
                                <a href="{{ route('initialBudgetMandate.index') }}"
                                    class="{{ Request::routeIs('initialBudgetMandate.*') ? 'active' : '' }}">
                                    <i data-feather="book"></i>
                                    <span data-key="t-initialMandate">{{ __('menus.initial.mandate') }}</span>
                                </a>
                            </li>
                        @endif
                        {{-- End initial budget Mandate --}}
                        <li>
                            <a href="javascript: void(0);" class="has-arrow">
                                <i data-feather="file-text"></i>
                                <span data-key="t-cluster">{{ __('menus.cluster') }}</span>
                            </a>
                            <ul class="sub-menu" aria-expanded="false">
                                @if (hasPermission('initialChapter.index'))
                                    <li class="{{ Request::routeIs('initialChapter.*') ? 'mm-active' : '' }}">

                                        <a href="{{ route('initialChapter.index') }}"
                                            class="{{ Request::routeIs('initialChapter.*') ? 'active' : '' }}">
                                            <i data-feather="book"></i>
                                            <span data-key="t-dashboard">{{ __('menus.chapter') }}</span>
                                        </a>
                                    </li>
                                @endif

                                @if (hasPermission('initialAccount.index'))
                                    <li class="{{ Request::routeIs('initialAccount.*') ? 'mm-active' : '' }}">
                                        <a href="{{ route('initialAccount.index') }}"
                                            class="{{ Request::routeIs('initialAccount.*') ? 'active' : '' }}">
                                            <i data-feather="database"></i>
                                            <span data-key="t-dashboard">{{ __('menus.account') }}</span>
                                        </a>
                                    </li>
                                @endif

                                @if (hasPermission('initialAccountSub.index'))
                                    <li class="{{ Request::routeIs('initialAccountSub.*') ? 'mm-active' : '' }}">
                                        <a href="{{ route('initialAccountSub.index') }}"
                                            class="{{ Request::routeIs('initialAccountSub.*') ? 'active' : '' }}">
                                            <i data-feather="layers"></i>
                                            <span data-key="t-dashboard">{{ __('menus.sub.account') }}</span>
                                        </a>
                                    </li>
                                @endif

                                @if (hasPermission('initialProgram.index'))
                                    <li class="{{ Request::routeIs('initialProgram.*') ? 'mm-active' : '' }}">
                                        <a href="{{ route('initialProgram.index') }}"
                                            class="{{ Request::routeIs('initialProgram.*') ? 'active' : '' }}">
                                            <i data-feather="layers" title="initialProgram"></i>

                                            <span data-key="t-dashboard">{{ __('menus.program') }}</span>
                                        </a>
                                    </li>
                                @endif

                                @if (hasPermission('initialProgramSub.index'))
                                    <li class="{{ Request::routeIs('initialProgramSub.*') ? 'mm-active' : '' }}">
                                        <a href="{{ route('initialProgramSub.index') }}"
                                            class="{{ Request::routeIs('initialProgramSub.*') ? 'active' : '' }}">
                                            <i data-feather="layers" title="initialProgramSub"></i>

                                            <span data-key="t-dashboard">{{ __('menus.sub.program') }}</span>
                                        </a>
                                    </li>
                                @endif

                                @if (hasPermission('initialAgency.index'))
                                    <li class="{{ Request::routeIs('initialAgency.*') ? 'mm-active' : '' }}">

                                        <a href="{{ route('initialAgency.index') }}"
                                            class="{{ Request::routeIs('initialAgency.*') ? 'active' : '' }}">
                                            <i data-feather="layers" title="Agency"></i>

                                            <span data-key="t-dashboard">{{ __('menus.agency') }}</span>
                                        </a>
                                    </li>
                                @endif

                            </ul>
                        </li>
                    </ul>
                </li>

                {{-- ========= Start Budget Plan ========= --}}
                <li>
                    <a href="javascript: void(0);" class="has-arrow">
                        <i data-feather="sliders"></i>
                        <span data-key="t-budget-plan">{{ __('menus.budget.plan') }}</span>
                    </a>
                    <ul class="sub-menu" aria-expanded="false">
                        @if (hasPermission('initialVoucher.index'))
                            <li class="{{ Request::routeIs('initialVoucher.*') ? 'mm-active' : '' }}">
                                <a href="{{ route('initialVoucher.index') }}"
                                    class="{{ Request::routeIs('initialVoucher.*') ? 'active' : '' }}">
                                    <i data-feather="file-plus"></i>
                                    <span data-key="t-budget-control-voucher">{{ __('menus.initial.voucher') }}</span>
                                </a>
                            </li>
                        @endif

                        @if (hasPermission('initialMandate.index'))
                            <li class="{{ Request::routeIs('initialMandate.*') ? 'mm-active' : '' }}">
                                <a href="{{ route('initialMandate.index') }}"
                                    class="{{ Request::routeIs('initialMandate.*') ? 'active' : '' }}">
                                    <i data-feather="file-plus"></i>
                                    <span data-key="t-budget-control-voucher">{{ __('menus.initial.mandate') }}</span>
                                </a>
                            </li>
                        @endif
                    </ul>
                </li>
                {{-- ========= End Budget Plan ========= --}}

                {{-- ========= Start Budget Control ========= --}}
                <li>
                    <a href="javascript: void(0);" class="has-arrow">
                        <i data-feather="pie-chart"></i>
                        <span data-key="t-pages">{{ __('menus.budget.control') }}</span>
                    </a>
                    <ul class="sub-menu" aria-expanded="false">

                        @if (hasPermission('voucherLoan.index'))
                            <li class="{{ Request::routeIs('voucherLoan.*') ? 'mm-active' : '' }}">
                                <a href="{{ route('voucherLoan.index') }}"
                                    class="{{ Request::routeIs('voucherLoan.*') ? 'active' : '' }}">
                                    <i data-feather="file-plus"></i>
                                    <span
                                        data-key="t-budget.control.voucherLoan">{{ __('menus.initial.voucher') }}</span>
                                </a>
                            </li>
                        @endif

                        @if (hasPermission('mandateLoan.index'))
                            <li class="{{ Request::routeIs('mandateLoan.*') ? 'mm-active' : '' }}">
                                <a href="{{ route('mandateLoan.index') }}"
                                    class="{{ Request::routeIs('mandateLoan.*') ? 'active' : '' }}">
                                    <i data-feather="briefcase"></i>
                                    <span data-key="t-dashboard">{{ __('menus.initial.mandate') }}</span>
                                </a>
                            </li>
                        @endif
                    </ul>
                </li>
                {{-- ========= End Budget Control ========= --}}

                {{-- @if (hasPermission('notes.index'))
                    <li class="{{ Request::routeIs('notes.*') ? 'mm-active' : '' }}">
                        <a href="{{ route('notes.index') }}"
                            class="{{ Request::routeIs('notes.*') ? 'active' : '' }}">
                            <i data-feather="clipboard"></i>
                            <span data-key="t-dashboard">{{ __('menus.notes') }}</span>
                        </a>
                    </li>
                @endif --}}

                {{-- ========= Start Electric & Water ========= --}}
                {{-- <li>
                    <a href="javascript: void(0);" class="has-arrow">
                        <i data-feather="zap"></i> 
                        <span data-key="t-pages">{{ __('menus.electric') . '-' . __('menus.water') }}</span>
                    </a>

                    <ul class="sub-menu" aria-expanded="false">
                        @if (hasPermission('voucher.index'))
                            <li class="{{ Request::routeIs('voucher.*') ? 'mm-active' : '' }}">
                                <a href="{{ route('voucher.index') }}"
                                    class="{{ Request::routeIs('voucher.*') ? 'active' : '' }}">
                                    <i data-feather="file-plus"></i>
                                    <span data-key="t-budget.control.voucher">{{ __('menus.electric') }}</span>
                                </a>
                            </li>
                        @endif

                        @if (hasPermission('account.index'))
                            <li class="{{ Request::routeIs('account.*') ? 'mm-active' : '' }}">
                                <a href="{{ route('account.index') }}"
                                    class="{{ Request::routeIs('account.*') ? 'active' : '' }}">
                                    <i data-feather="briefcase"></i>
                                    <span data-key="t-dashboard">{{ __('menus.water') }}</span>
                                </a>
                            </li>
                        @endif
                    </ul>
                </li> --}}
                {{-- ========= End Electric & Water ========= --}}

                {{-- ========= Start Duel & Material ========= --}}
                {{-- <li>
                    <a href="javascript: void(0);" class="has-arrow">
                        <i data-feather="zap"></i> 
                        <span data-key="t-pages">{{ __('menus.duel') . '-' . __('menus.material') }}</span>
                    </a>

                    <ul class="sub-menu" aria-expanded="false">
                        @if (hasPermission('voucher.index'))
                            <li class="{{ Request::routeIs('voucher.*') ? 'mm-active' : '' }}">
                                <a href="{{ route('voucher.index') }}"
                                    class="{{ Request::routeIs('voucher.*') ? 'active' : '' }}">
                                    <i data-feather="file-plus"></i>
                                    <span data-key="t-budget.control.voucher">{{ __('menus.duel') }}</span>
                                </a>
                            </li>
                        @endif

                        @if (hasPermission('account.index'))
                            <li class="{{ Request::routeIs('account.*') ? 'mm-active' : '' }}">
                                <a href="{{ route('account.index') }}"
                                    class="{{ Request::routeIs('account.*') ? 'active' : '' }}">
                                    <i data-feather="briefcase"></i>
                                    <span data-key="t-dashboard">{{ __('menus.material') }}</span>
                                </a>
                            </li>
                        @endif
                    </ul>
                </li> --}}
                {{-- ========= End Duel & Material ========= --}}

                {{-- ========= Start Report ========= --}}
                <li>
                    <a href="javascript: void(0);" class="has-arrow">
                        <i data-feather="zap"></i>
                        <span data-key="t-pages">{{ __('menus.check.control.general') }}</span>
                    </a>

                    <ul class="sub-menu" aria-expanded="false">

                        @if (hasPermission('account.index'))
                            <li class="{{ Request::routeIs('account.*') ? 'mm-active' : '' }}">
                                <a href="" class="{{ Request::routeIs('account.*') ? 'active' : '' }}">
                                    <i data-feather="briefcase"></i>
                                    <span data-key="t-dashboard"> ឥណទានសរុប</span>
                                </a>
                            </li>
                        @endif


                        @if (hasPermission('account.index'))
                            <li class="{{ Request::routeIs('account.*') ? 'mm-active' : '' }}">
                                <a href="" class="{{ Request::routeIs('account.*') ? 'active' : '' }}">
                                    <i data-feather="briefcase"></i>
                                    <span data-key="t-dashboard"> របាយការណ៍អនុវត្ត</span>
                                </a>
                            </li>
                        @endif



                        @if (hasPermission('account.index'))
                            <li class="{{ Request::routeIs('account.*') ? 'mm-active' : '' }}">
                                <a href="" class="{{ Request::routeIs('account.*') ? 'active' : '' }}">
                                    <i data-feather="briefcase"></i>
                                    <span data-key="t-dashboard">{{ __('menus.check.control.summary') }}</span>
                                </a>
                            </li>
                        @endif

                        @if (hasPermission('guarantee.index'))
                            <li class="{{ Request::routeIs('guarantee.*') ? 'mm-active' : '' }}">
                                <a href="{{ route('guarantee.index') }}"
                                    class="{{ Request::routeIs('guarantee.*') ? 'active' : '' }}">
                                    <i data-feather="file-plus"></i>
                                    <span
                                        data-key="t-check.control.guarantee">{{ __('menus.check.control.guarantee') }}</span>
                                </a>
                            </li>
                        @endif

                        @if (hasPermission('account.index'))
                            <li class="{{ Request::routeIs('account.*') ? 'mm-active' : '' }}">
                                <a href="" class="{{ Request::routeIs('account.*') ? 'active' : '' }}">
                                    <i data-feather="briefcase"></i>
                                    <span data-key="t-dashboard"> តារាង​ច្បាប់ អាណត្តិ និងសលាកបត្រ</span>
                                </a>
                            </li>
                        @endif


                    </ul>
                </li>

                {{-- <li>
                    <a href="javascript: void(0);" class="has-arrow">
                        <i data-feather="sliders"></i>
                        <span data-key="t-tables">{{ __('menus.check.control.general') }}</span>
                    </a>
                    <ul class="sub-menu" aria-expanded="false">
                        <li class="{{ Request::routeIs('.*') ? 'mm-active' : '' }}">
                            <a href="" class="{{ Request::routeIs('.*') ? 'active' : '' }}"><span
                                    data-key="t-check.control.guarantee">{{ __('menus.check.control.guarantee') }}</span></a>
                        </li>
                        <li><a href="tables-datatable.html" data-key="t-data-tables"><span
                                    data-key="t-check.control.summary">{{ __('menus.check.control.summary') }}</span></a>
                        </li>
                        <li><a href="tables-responsive.html" data-key="t-responsive-table">Responsive</a></li>
                        <li><a href="tables-editable.html" data-key="t-editable-table">Editable</a></li>
                    </ul>
                </li> --}}
                {{-- ========= End Report ========= --}}


                @if (auth()->user()->role_id == 1)
                    <li class="menu-title" data-key="t-setting">{{ __('menus.setting') }}</li>
                    <li class="{{ Request::routeIs('system.*') ? 'mm-active' : '' }}">
                        <a href="{{ route('system.index') }}"
                            class="{{ Request::routeIs('system.*') ? 'active' : '' }}">
                            <i data-feather="database"></i>
                            <span data-key="t-roles">{{ __('menus.setting.log') }}</span>
                        </a>
                    </li>
                    <li class="{{ Request::routeIs('keys.*') ? 'mm-active' : '' }}">
                        <a href="{{ route('keys.index') }}"
                            class="{{ Request::routeIs('keys.*') ? 'active' : '' }}">
                            <i data-feather="shield"></i>
                            <span data-key="t-roles">{{ __('menus.api.key') }}</span>
                        </a>
                    </li>
                    <li class="{{ Request::routeIs('category.*') ? 'mm-active' : '' }}">
                        <a href="{{ route('category.index') }}"
                            class="{{ Request::routeIs('category.*') ? 'active' : '' }}">
                            <i data-feather="folder"></i>
                            <span data-key="t-roles">{{ __('menus.setting.category') }}</span>
                        </a>
                    </li>
                    <li class="{{ Request::routeIs('role.*') ? 'mm-active' : '' }}">
                        <a href="{{ route('role.index') }}">
                            <i data-feather="sliders"></i>
                            <span data-key="t-roles">{{ __('menus.setting.roles') }}</span>
                        </a>
                    </li>
                    <li class="{{ Request::routeIs('user.*') ? 'mm-active' : '' }}">
                        <a href="{{ route('user.index') }}">
                            <i data-feather="users"></i>
                            <span data-key="t-member">{{ __('menus.setting.member') }}</span>
                        </a>
                    </li>
                @endif
                @if (hasPermission('category.index') and auth()->user()->role_id != 1)
                    <li class="menu-title" data-key="t-setting">{{ __('menus.setting') }}</li>
                    <li class="{{ Request::routeIs('category.*') ? 'mm-active' : '' }}">
                        <a href="{{ route('category.index') }}"
                            class="{{ Request::routeIs('category.*') ? 'active' : '' }}">
                            <i data-feather="folder"></i>
                            <span data-key="t-roles">{{ __('menus.setting.category') }}</span>
                        </a>
                    </li>
                @endif
            </ul>
        </div>
    </div>
</div>
