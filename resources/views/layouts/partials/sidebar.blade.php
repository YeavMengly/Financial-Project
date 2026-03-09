<div class="vertical-menu">
    <div data-simplebar class="h-100">
        <div id="sidebar-menu">
            <!-- Left Menu Start -->
            <ul class="metismenu list-unstyled" id="side-menu">

                {{-- Dashboard --}}
                <li class="{{ Request::routeIs('dashboard.*') ? 'mm-active' : '' }}">
                    <a href="{{ route('dashboard.index') }}"
                        class="{{ Request::routeIs('dashboard.*') ? 'active' : '' }}">
                        <i data-feather="home"></i>
                        <span data-key="t-dashboard">{{ __('menus.dashboard') }}</span>
                    </a>
                </li>

                {{-- ========== Beginning Credit ========== --}}
                <li class="menu-title" data-key="t-inventory">{{ __('menus.inventory') }}</li>
                @if (hasPermission('ministries.index'))
                    <li>
                        <a href="{{ route('initialBudgetVoucher.index') }}"
                            class="{{ Request::routeIs('initialBudgetVoucher.*') ? 'active' : '' }}">
                            <i data-feather="book"></i>
                            <span data-key="t-initialVoucher">{{ __('menus.credit') }}</span>
                        </a>
                    </li>
                @endif
                {{-- @php
                    $beginCreditActive =
                        Request::routeIs('initialBudgetVoucher.*') ||
                        Request::routeIs('initialBudgetMandate.*') ||
                        Request::routeIs('beginVoucher.*') ||
                        Request::routeIs('beginMandate.*');
                @endphp --}}

                {{-- <li class="{{ $beginCreditActive ? 'mm-active' : '' }}">
                    <a href="javascript: void(0);" class="has-arrow">
                        <i data-feather="credit-card"></i>
                        <span data-key="t-beginning-credit">{{ __('menus.credit') }}</span>
                    </a>
                    <ul class="sub-menu {{ $beginCreditActive ? 'mm-show' : '' }}"
                        aria-expanded="{{ $beginCreditActive ? 'true' : 'false' }}">

                        @if (hasPermission('ministries.index'))
                            <li>
                                <a href="{{ route('initialBudgetVoucher.index') }}"
                                    class="{{ Request::routeIs('initialBudgetVoucher.*') ? 'active' : '' }}">
                                    <i data-feather="book"></i>
                                    <span data-key="t-initialVoucher">{{ __('menus.initial.voucher') }}</span>
                                </a>
                            </li>
                        @endif

                        @if (hasPermission('ministries.index'))
                            <li>
                                <a href="{{ route('initialBudgetMandate.index') }}"
                                    class="{{ Request::routeIs('initialBudgetMandate.*') ? 'active' : '' }}">
                                    <i data-feather="book"></i>
                                    <span data-key="t-initialMandate">{{ __('menus.initial.mandate') }}</span>
                                </a>
                            </li>
                        @endif
                    </ul>
                </li> --}}

                {{-- ========== Budget Plan ========== --}}
                @php
                    $budgetPlanActive =
                        Request::routeIs('initialVoucher.*') ||
                        Request::routeIs('initialMandate.*') ||
                        Request::routeIs('budgetVoucher.*') ||
                        Request::routeIs('budgetMandate.*');
                @endphp
                <li class="{{ $budgetPlanActive ? 'mm-active' : '' }}">
                    <a href="javascript: void(0);" class="has-arrow">
                        <i data-feather="sliders"></i>
                        <span data-key="t-budget-plan">{{ __('menus.budget.plan') }}</span>
                    </a>
                    <ul class="sub-menu {{ $budgetPlanActive ? 'mm-show' : '' }}"
                        aria-expanded="{{ $budgetPlanActive ? 'true' : 'false' }}">

                        @if (hasPermission('ministries.index'))
                            <li>
                                <a href="{{ route('initialMandate.index') }}"
                                    class="{{ Request::routeIs('initialMandate.*') ? 'active' : '' }}">
                                    <i data-feather="file-plus"></i>
                                    <span data-key="t-budget-control-mandate">
                                        {{ __('menus.expenditure.guarantee') }}
                                    </span>
                                </a>
                            </li>
                        @endif

                        @if (hasPermission('ministries.index'))
                            <li>
                                <a href="{{ route('initialVoucher.index') }}"
                                    class="{{ Request::routeIs('initialVoucher.*') ? 'active' : '' }}">
                                    <i data-feather="file-plus"></i>
                                    <span data-key="t-budget-control-voucher">
                                        {{ __('menus.payment') }}
                                    </span>
                                </a>
                            </li>
                        @endif

                        {{-- @if (hasPermission('ministries.index'))
                            <li>
                                <a href="{{ url('maintenance') }}"
                                    class="{{ Request::routeIs('initialVoucher.*') ? 'active' : '' }}">
                                    <i data-feather="file-plus"></i>
                                    <span data-key="t-budget-control-procurement">
                                        {{ __('menus.procurement') }}
                                    </span>
                                </a>
                            </li>
                        @endif --}}
                    </ul>
                </li>

                {{-- ========== Budget Control ========== --}}
                @php
                    $budgetControlActive =
                        Request::routeIs('voucherLoan.*') ||
                        Request::routeIs('mandateLoan.*') ||
                        Request::routeIs('voucher.*') ||
                        Request::routeIs('mandate.*');
                @endphp
                <li class="{{ $budgetControlActive ? 'mm-active' : '' }}">
                    <a href="javascript: void(0);" class="has-arrow">
                        <i data-feather="pie-chart"></i>
                        <span data-key="t-pages">{{ __('menus.budget.control') }}</span>
                    </a>
                    <ul class="sub-menu {{ $budgetControlActive ? 'mm-show' : '' }}"
                        aria-expanded="{{ $budgetControlActive ? 'true' : 'false' }}">

                        @if (hasPermission('ministries.index'))
                            <li>
                                <a href="{{ route('voucherLoan.index') }}"
                                    class="{{ Request::routeIs('voucherLoan.*') ? 'active' : '' }}">
                                    <i data-feather="file-plus"></i>
                                    <span data-key="t-budget.control.voucherLoan">
                                        {{ __('menus.initial.voucher') }}
                                    </span>
                                </a>
                            </li>
                        @endif

                        @if (hasPermission('ministries.index'))
                            <li>
                                <a href="{{ route('mandateLoan.index') }}"
                                    class="{{ Request::routeIs('mandateLoan.*') ? 'active' : '' }}">
                                    <i data-feather="briefcase"></i>
                                    <span data-key="t-dashboard">{{ __('menus.initial.mandate') }}</span>
                                </a>
                            </li>
                        @endif
                    </ul>
                </li>

                {{-- ========== Check Control General (Reports) ========== --}}
                @php
                    $reportActive = false;
                @endphp
                {{-- <li class="{{ $reportActive ? 'mm-active' : '' }}">
                    <a href="javascript: void(0);" class="has-arrow">
                        <i data-feather="zap"></i>
                        <span data-key="t-reprots">{{ __('menus.check.control.general') }}</span>
                    </a>

                    <ul class="sub-menu {{ $reportActive ? 'mm-show' : '' }}"
                        aria-expanded="{{ $reportActive ? 'true' : 'false' }}">

                        @if (hasPermission('ministries.index'))
                            <li>
                                <a href="#" class="">
                                    <i data-feather="file"></i>
                                    <span data-key="t-report-program">{{ __('menus.report.program') }}</span>
                                </a>
                            </li>
                        @endif
                        @if (hasPermission('ministries.index'))
                            <li>
                                <a href="#" class="">
                                    <i data-feather="file"></i>
                                    <span data-key="t-report-program">{{ __('menus.report') }}</span>
                                </a>
                            </li>
                        @endif
                    </ul>
                </li> --}}

                {{-- <li class="menu-title" data-key="t-inventory">{{ __('menus.inventory') }}</li> --}}

                {{-- ========== Duel ========== --}}
                @php
                    $duelActive =
                        Request::routeIs('initialDuelEntry.*') ||
                        Request::routeIs('initialDuelRelease.*') ||
                        Request::routeIs('duelEntry.*') ||
                        Request::routeIs('duelRelease.*');
                @endphp
                <li class="{{ $duelActive ? 'mm-active' : '' }}">
                    <a href="javascript: void(0);" class="has-arrow">
                        <i data-feather="file-text"></i>
                        <span data-key="t-duel">{{ __('menus.duel') }}</span>
                    </a>
                    <ul class="sub-menu {{ $duelActive ? 'mm-show' : '' }}"
                        aria-expanded="{{ $duelActive ? 'true' : 'false' }}">

                        @if (hasPermission('ministries.index'))
                            <li>
                                <a href="{{ route('initialDuelEntry.index') }}"
                                    class="{{ Request::routeIs('initialDuelEntry.*') ? 'active' : '' }}">
                                    <i data-feather="crosshair"></i>
                                    <span data-key="t-duel">{{ __('menus.duel.entry') }}</span>
                                </a>
                            </li>
                        @endif

                        @if (hasPermission('ministries.index'))
                            <li>
                                <a href="{{ route('initialDuelRelease.index') }}"
                                    class="{{ Request::routeIs('initialDuelRelease.*') ? 'active' : '' }}">
                                    <i data-feather="package"></i>
                                    <span data-key="t-duel">{{ __('menus.duel.release') }}</span>
                                </a>
                            </li>
                        @endif
                    </ul>
                </li>

                {{-- ========== Material ========== --}}
                @php
                    $materialActive =
                        Request::routeIs('initialMaterialEntry.*') ||
                        Request::routeIs('initialMaterialRelease.*') ||
                        Request::routeIs('materialEntry.*') ||
                        Request::routeIs('materialRelease.*');
                @endphp
                <li class="{{ $materialActive ? 'mm-active' : '' }}">
                    <a href="javascript: void(0);" class="has-arrow">
                        <i data-feather="file-text"></i>
                        <span data-key="t-inventory">{{ __('menus.material') }}</span>
                    </a>
                    <ul class="sub-menu {{ $materialActive ? 'mm-show' : '' }}"
                        aria-expanded="{{ $materialActive ? 'true' : 'false' }}">

                        @if (hasPermission('ministries.index'))
                            <li>
                                <a href="{{ route('initialMaterialEntry.index') }}"
                                    class="{{ Request::routeIs('initialMaterialEntry.*') ? 'active' : '' }}">
                                    <i data-feather="git-merge"></i>
                                    <span data-key="t-duel">{{ __('menus.material.entry') }}</span>
                                </a>
                            </li>
                        @endif

                        @if (hasPermission('ministries.index'))
                            <li>
                                <a href="{{ route('initialMaterialRelease.index') }}"
                                    class="{{ Request::routeIs('initialMaterialRelease.*') ? 'active' : '' }}">
                                    <i data-feather="package"></i>
                                    <span data-key="t-material">{{ __('menus.material.release') }}</span>
                                </a>
                            </li>
                        @endif
                    </ul>
                </li>

                {{-- ========== Water ========== --}}
                @php
                    $waterActive =
                        Request::routeIs('initialWater.*') ||
                        Request::routeIs('initialWaterEntity.*') ||
                        Request::routeIs('water.*') ||
                        Request::routeIs('waterEntity.*');
                @endphp
                <li class="{{ $waterActive ? 'mm-active' : '' }}">
                    <a href="javascript: void(0);" class="has-arrow">
                        <i data-feather="file-text"></i>
                        <span data-key="t-inventory">{{ __('menus.water') }}</span>
                    </a>
                    <ul class="sub-menu {{ $waterActive ? 'mm-show' : '' }}"
                        aria-expanded="{{ $waterActive ? 'true' : 'false' }}">

                        @if (hasPermission('ministries.index'))
                            <li>
                                <a href="{{ route('initialWater.index') }}"
                                    class="{{ Request::routeIs('initialWater.*') ? 'active' : '' }}">
                                    <i data-feather="git-merge"></i>
                                    <span data-key="t-water">{{ __('menus.water.entry') }}</span>
                                </a>
                            </li>
                        @endif

                        @if (hasPermission('ministries.index'))
                            <li>
                                <a href="{{ route('initialWaterEntity.index') }}"
                                    class="{{ Request::routeIs('initialWaterEntity.*') ? 'active' : '' }}">
                                    <i data-feather="package"></i>
                                    <span data-key="t-water">{{ __('menus.water.entity') }}</span>
                                </a>
                            </li>
                        @endif
                    </ul>
                </li>

                {{-- ========== Electric ========== --}}
                @php
                    $electricActive =
                        Request::routeIs('initialElectric.*') ||
                        Request::routeIs('initialElectricEntity.*') ||
                        Request::routeIs('electric.*') ||
                        Request::routeIs('electricEntity.*');
                @endphp
                <li class="{{ $electricActive ? 'mm-active' : '' }}">
                    <a href="javascript: void(0);" class="has-arrow">
                        <i data-feather="file-text"></i>
                        <span data-key="t-inventory">{{ __('menus.electric') }}</span>
                    </a>

                    <ul class="sub-menu {{ $electricActive ? 'mm-show' : '' }}"
                        aria-expanded="{{ $electricActive ? 'true' : 'false' }}">

                        @if (hasPermission('ministries.index'))
                            <li>
                                <a href="{{ route('initialElectric.index') }}"
                                    class="{{ Request::routeIs('initialElectric.*') ? 'active' : '' }}">
                                    <i data-feather="git-merge"></i>
                                    <span>{{ __('menus.electric.entry') }}</span>
                                </a>
                            </li>
                        @endif

                        @if (hasPermission('ministries.index'))
                            <li>
                                <a href="{{ route('initialElectricEntity.index') }}"
                                    class="{{ Request::routeIs('initialElectricEntity.*') ? 'active' : '' }}">
                                    <i data-feather="zap"></i>
                                    <span>{{ __('menus.electric.entity') }}</span>
                                </a>
                            </li>
                        @endif
                    </ul>
                </li>

                <li class="menu-title" data-key="t-content">{{ __('menus.content') }}</li>

                {{-- ========== Cluster ========== --}}
                {{-- <li class="menu-title">{{ __('menus.cluster') }}</li> --}}

                @if (hasPermission('ministries.index'))
                    <li class="{{ Request::routeIs('ministries.*') ? 'mm-active' : '' }}">
                        <a href="{{ route('ministries.index') }}"
                            class="{{ Request::routeIs('ministries.*') ? 'active' : '' }}">
                            <i data-feather="book"></i>
                            <span>{{ __('menus.create.year') }}</span>
                        </a>
                    </li>
                @endif

                @if (hasPermission('ministries.index'))
                    <li class="{{ Request::routeIs('initialChapter.*') ? 'mm-active' : '' }}">
                        <a href="{{ route('initialChapter.index') }}"
                            class="{{ Request::routeIs('initialChapter.*') ? 'active' : '' }}">
                            <i data-feather="book"></i>
                            <span>{{ __('menus.chapters') }}</span>
                        </a>
                    </li>
                @endif

                {{-- @if (hasPermission('ministries.index'))
                    <li class="{{ Request::routeIs('initialAccount.*') ? 'mm-active' : '' }}">
                        <a href="{{ route('initialAccount.index') }}"
                            class="{{ Request::routeIs('initialAccount.*') ? 'active' : '' }}">
                            <i data-feather="database"></i>
                            <span>{{ __('menus.accounts') }}</span>
                        </a>
                    </li>
                @endif --}}

                {{-- @if (hasPermission('ministries.index'))
                    <li class="{{ Request::routeIs('initialAccountSub.*') ? 'mm-active' : '' }}">
                        <a href="{{ route('initialAccountSub.index') }}"
                            class="{{ Request::routeIs('initialAccountSub.*') ? 'active' : '' }}">
                            <i data-feather="layers"></i>
                            <span>{{ __('menus.sub.account') }}</span>
                        </a>
                    </li>
                @endif --}}

                @if (hasPermission('ministries.index'))
                    <li class="{{ Request::routeIs('initialProgram.*') ? 'mm-active' : '' }}">
                        <a href="{{ route('initialProgram.index') }}"
                            class="{{ Request::routeIs('initialProgram.*') ? 'active' : '' }}">
                            <i data-feather="layers"></i>
                            <span>{{ __('menus.program') }}</span>
                        </a>
                    </li>
                @endif

                @if (hasPermission('ministries.index'))
                    <li class="{{ Request::routeIs('initialAgency.*') ? 'mm-active' : '' }}">
                        <a href="{{ route('initialAgency.index') }}"
                            class="{{ Request::routeIs('initialAgency.*') ? 'active' : '' }}">
                            <i data-feather="layers"></i>
                            <span>{{ __('menus.agency') }}</span>
                        </a>
                    </li>
                @endif

                @if (hasPermission('ministries.index'))
                    <li>
                        <a href="{{ route('expenseType.index') }}"
                            class="{{ Request::routeIs('expenseType.*') ? 'active' : '' }}">
                            <i data-feather="layers" title="expense_ty"></i>
                            <span data-key="t-dashboard">{{ __('menus.expense.type') }}</span>
                        </a>
                    </li>
                @endif

                {{-- ========== Setting ========== --}}
                @if (auth()->user()->role_id == 1)
                    <li class="menu-title" data-key="t-setting">{{ __('menus.setting') }}</li>

                    <li class="{{ Request::routeIs('system.*') ? 'mm-active' : '' }}">
                        <a href="{{ route('system.index') }}"
                            class="{{ Request::routeIs('system.*') ? 'active' : '' }}">
                            <i data-feather="database"></i>
                            <span data-key="t-roles">{{ __('menus.setting.log') }}</span>
                        </a>
                    </li>

                    {{-- <li class="{{ Request::routeIs('keys.*') ? 'mm-active' : '' }}">
                        <a href="{{ route('keys.index') }}"
                            class="{{ Request::routeIs('keys.*') ? 'active' : '' }}">
                            <i data-feather="shield"></i>
                            <span data-key="t-roles">{{ __('menus.api.key') }}</span>
                        </a>
                    </li> --}}

                    {{-- <li class="{{ Request::routeIs('category.*') ? 'mm-active' : '' }}">
                        <a href="{{ route('category.index') }}"
                            class="{{ Request::routeIs('category.*') ? 'active' : '' }}">
                            <i data-feather="folder"></i>
                            <span data-key="t-roles">{{ __('menus.setting.category') }}</span>
                        </a>
                    </li> --}}

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
