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
                @if (hasPermission('initialBudgetVoucher.index'))
                    <li>
                        <a href="{{ route('initialBudgetVoucher.index') }}"
                            class="{{ Request::routeIs('initialBudgetVoucher.*') ? 'active' : '' }}">
                            <i data-feather="book"></i>
                            <span data-key="t-initialVoucher">{{ __('menus.credit') }}</span>
                        </a>
                    </li>
                @endif

                {{-- ========== Budget Plan ========== --}}
                @php
                    // Refactored to pass an array to routeIs() for clean readability
                    $budgetPlanActive = Request::routeIs([
                        'initialMandate.*',
                        'initialProcurement.*',
                        'initialAdvancePayment.*',
                        'initialVoucher.*',
                        'initialDirectPayment.*',
                        'initialRoyaltyMandate.*',
                        'initialRoyaltyVoucher.*',
                        'budgetVoucher.*',
                        'budgetMandate.*',
                        'budgetAdvancePayment.*',
                        'budgetDirectPayment.*',
                        'royaltyMandate.*',
                        'royaltyVoucher.*',
                    ]);

                    $directPaymentActive = Request::routeIs('initialDirectPayment.*');
                    $preFinancingActive = Request::routeIs(['initialRoyaltyMandate.*', 'initialRoyaltyVoucher.*']);
                @endphp

                <li class="{{ $budgetPlanActive ? 'mm-active' : '' }}">
                    <a href="javascript: void(0);" class="has-arrow">
                        <i data-feather="sliders"></i>
                        <span data-key="t-budget-plan">{{ __('menus.budget.plan') }}</span>
                    </a>

                    <ul class="sub-menu {{ $budgetPlanActive ? 'mm-show' : '' }}"
                        aria-expanded="{{ $budgetPlanActive ? 'true' : 'false' }}">

                        {{-- Guarantee / Mandate --}}
                        @if (hasPermission('initialMandate.index'))
                            <li>
                                <a href="{{ route('initialMandate.index') }}"
                                    class="{{ Request::routeIs('initialMandate.*') ? 'active' : '' }}">
                                    <i data-feather="file-plus"></i>
                                    <span
                                        data-key="t-budget-control-mandate">{{ __('menus.expenditure.guarantee') }}</span>
                                </a>
                            </li>
                        @endif

                        {{-- Procurement --}}
                        @if (hasPermission('initialProcurement.index'))
                            <li>
                                <a href="{{ route('initialProcurement.index') }}"
                                    class="{{ Request::routeIs('initialProcurement.*') ? 'active' : '' }}">
                                    <i data-feather="file-plus"></i>
                                    <span
                                        data-key="t-budget-control-procurement">{{ __('menus.expenditure.procurement') }}</span>
                                </a>
                            </li>
                        @endif

                        {{-- Advance Payment --}}
                        @if (hasPermission('initialAdvancePayment.index'))
                            <li>
                                <a href="{{ route('initialAdvancePayment.index') }}"
                                    class="{{ Request::routeIs('initialAdvancePayment.*') ? 'active' : '' }}">
                                    <i data-feather="file-plus"></i>
                                    <span
                                        data-key="t-budget-control-advance-payment">{{ __('menus.advance.payment') }}</span>
                                </a>
                            </li>
                        @endif

                        {{-- Section Divider --}}
                        <li class="menu-title" data-key="t-payment-title">{{ __('menus.payment') }}</li>

                        {{-- Voucher Payment --}}
                        @if (hasPermission('initialVoucher.index'))
                            <li>
                                <a href="{{ route('initialVoucher.index') }}"
                                    class="{{ Request::routeIs('initialVoucher.*') ? 'active' : '' }}">
                                    <i data-feather="file-plus"></i>
                                    <span data-key="t-budget-control-voucher">{{ __('menus.payment') }}</span>
                                </a>
                            </li>
                        @endif

                        {{-- Direct Payment Submenu --}}
                        <li class="{{ $directPaymentActive ? 'mm-active' : '' }}">
                            <a href="javascript: void(0);"
                                class="has-arrow {{ $directPaymentActive ? 'active' : '' }}">
                                <i data-feather="sliders"></i>
                                <span data-key="t-direct-payment">{{ __('menus.direct.payment') }}</span>
                            </a>
                            <ul class="sub-menu {{ $directPaymentActive ? 'mm-show' : '' }}"
                                aria-expanded="{{ $directPaymentActive ? 'true' : 'false' }}">
                                @if (hasPermission('initialDirectPayment.expenseRecord.index'))
                                    <li>
                                        <a href="{{ route('initialDirectPayment.expenseRecord.index') }}"
                                            class="{{ Request::routeIs('initialDirectPayment.expenseRecord.*') ? 'active' : '' }}">
                                            <i data-feather="file-plus"></i>
                                            <span
                                                data-key="t-direct-expense-record">{{ __('menus.expense.record.book') }}</span>
                                        </a>
                                    </li>
                                @endif

                                @if (hasPermission('initialDirectPayment.paymentDeadline.index'))
                                    <li>
                                        <a href="{{ route('initialDirectPayment.paymentDeadline.index') }}"
                                            class="{{ Request::routeIs('initialDirectPayment.paymentDeadline.*') ? 'active' : '' }}">
                                            <i data-feather="file-plus"></i>
                                            <span
                                                data-key="t-direct-payment-deadline">{{ __('menus.payment.deadline') }}</span>
                                        </a>
                                    </li>
                                @endif
                            </ul>
                        </li>

                        {{-- Pre-Financing Submenu --}}
                        <li class="{{ $preFinancingActive ? 'mm-active' : '' }}">
                            <a href="javascript: void(0);" class="has-arrow {{ $preFinancingActive ? 'active' : '' }}">
                                <i data-feather="sliders"></i>
                                <span data-key="t-pre-financing">{{ __('menus.pre.financing') }}</span>
                            </a>
                            <ul class="sub-menu {{ $preFinancingActive ? 'mm-show' : '' }}"
                                aria-expanded="{{ $preFinancingActive ? 'true' : 'false' }}">

                                {{-- Nested Royalty Menu --}}
                                @if (hasPermission('initialRoyaltyMandate.index') || hasPermission('initialRoyaltyVoucher.index'))
                                    <li class="{{ $preFinancingActive ? 'mm-active' : '' }}">
                                        <a href="javascript:void(0);"
                                            class="has-arrow {{ $preFinancingActive ? 'active' : '' }}">
                                            <i data-feather="file-plus"></i>
                                            <span data-key="t-royalty-menu">{{ __('menus.royalty') }}</span>
                                        </a>
                                        <ul class="sub-menu {{ $preFinancingActive ? 'mm-show' : '' }}"
                                            aria-expanded="{{ $preFinancingActive ? 'true' : 'false' }}">
                                            @if (hasPermission('initialRoyaltyMandate.index'))
                                                <li>
                                                    <a href="{{ route('initialRoyaltyMandate.index') }}"
                                                        class="{{ Request::routeIs('initialRoyaltyMandate.*') ? 'active' : '' }}">
                                                        <i data-feather="file-plus"></i>
                                                        {{ __('menus.expense.record.book') }}
                                                    </a>
                                                </li>
                                            @endif

                                            @if (hasPermission('initialRoyaltyVoucher.index'))
                                                <li>
                                                    <a href="{{ route('initialRoyaltyVoucher.index') }}"
                                                        class="{{ Request::routeIs('initialRoyaltyVoucher.*') ? 'active' : '' }}">
                                                        <i data-feather="file-plus"></i>
                                                        {{ __('menus.payment.deadline') }}
                                                    </a>
                                                </li>
                                            @endif
                                        </ul>
                                    </li>
                                @endif

                                {{-- Note: Update these route names/permissions if you have separate ones for Missions and Training --}}
                                @if (hasPermission('initialDirectPayment.paymentDeadline.index'))
                                    <li>
                                        <a href="{{ route('initialDirectPayment.paymentDeadline.index') }}"
                                            class="{{ Request::routeIs('initialDirectPayment.paymentDeadline.*') ? 'active' : '' }}">
                                            <i data-feather="file-plus"></i>
                                            <span data-key="t-missions">{{ __('menus.missions') }}</span>
                                        </a>
                                    </li>
                                @endif

                                @if (hasPermission('initialDirectPayment.paymentDeadline.index'))
                                    <li>
                                        <a href="{{ route('initialDirectPayment.paymentDeadline.index') }}"
                                            class="{{ Request::routeIs('initialDirectPayment.paymentDeadline.*') ? 'active' : '' }}">
                                            <i data-feather="file-plus"></i>
                                            <span data-key="t-training">{{ __('menus.training') }}</span>
                                        </a>
                                    </li>
                                @endif
                            </ul>
                        </li>

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

                @if (hasPermission('voucherLoan.index'))
                    <li>
                        <a href="{{ route('voucherLoan.index') }}"
                            class="{{ Request::routeIs('voucherLoan.*') ? 'active' : '' }}">
                            <i data-feather="pie-chart"></i>
                            <span data-key="t-budget.control.voucherLoan">
                                {{ __('menus.budget.control') }}
                            </span>
                        </a>
                    </li>
                @endif

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

                        @if (hasPermission('initialDuelEntry.index'))
                            <li>
                                <a href="{{ route('initialDuelEntry.index') }}"
                                    class="{{ Request::routeIs('initialDuelEntry.*') ? 'active' : '' }}">
                                    <i data-feather="crosshair"></i>
                                    <span data-key="t-duel-entry">{{ __('menus.duel.entry') }}</span>
                                </a>
                            </li>
                        @endif

                        @if (hasPermission('initialDuelRelease.index'))
                            <li>
                                <a href="{{ route('initialDuelRelease.index') }}"
                                    class="{{ Request::routeIs('initialDuelRelease.*') ? 'active' : '' }}">
                                    <i data-feather="package"></i>
                                    <span data-key="t-duel-release">{{ __('menus.duel.release') }}</span>
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
                        <span data-key="t-material">{{ __('menus.material') }}</span>
                    </a>

                    <ul class="sub-menu {{ $materialActive ? 'mm-show' : '' }}"
                        aria-expanded="{{ $materialActive ? 'true' : 'false' }}">

                        {{-- Material Entry --}}
                        @if (hasPermission('initialMaterialEntry.index'))
                            <li>
                                <a href="{{ route('initialMaterialEntry.index') }}"
                                    class="{{ Request::routeIs('initialMaterialEntry.*') ? 'active' : '' }}">
                                    <i data-feather="git-merge"></i>
                                    <span data-key="t-material-entry">{{ __('menus.material.entry') }}</span>
                                </a>
                            </li>
                        @endif

                        {{-- Material Release --}}
                        @if (hasPermission('initialMaterialRelease.index'))
                            <li>
                                <a href="{{ route('initialMaterialRelease.index') }}"
                                    class="{{ Request::routeIs('initialMaterialRelease.*') ? 'active' : '' }}">
                                    <i data-feather="package"></i>
                                    <span data-key="t-material-release">{{ __('menus.material.release') }}</span>
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

                        @if (hasPermission('initialWater.index'))
                            <li>
                                <a href="{{ route('initialWater.index') }}"
                                    class="{{ Request::routeIs('initialWater.*') ? 'active' : '' }}">
                                    <i data-feather="git-merge"></i>
                                    <span data-key="t-water">{{ __('menus.water.entry') }}</span>
                                </a>
                            </li>
                        @endif

                        @if (hasPermission('initialWaterEntity.index'))
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

                        @if (hasPermission('initialElectric.index'))
                            <li>
                                <a href="{{ route('initialElectric.index') }}"
                                    class="{{ Request::routeIs('initialElectric.*') ? 'active' : '' }}">
                                    <i data-feather="git-merge"></i>
                                    <span>{{ __('menus.electric.entry') }}</span>
                                </a>
                            </li>
                        @endif

                        @if (hasPermission('initialElectricEntity.index'))
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

                {{-- ========== Reports ========== --}}
                <li class="menu-title" data-key="t-reports">{{ __('menus.reports') }}</li>
                <li class="{{ Request::routeIs('cost.implement.agency.*') ? 'mm-active' : '' }}">
                    <a href="{{ route('cost.implement.agency.index') }}"
                        class="{{ Request::routeIs('cost.implement.agency.*') ? 'active' : '' }}">
                        <i data-feather="folder"></i>
                        <span data-key="t-cost.implement.agency">{{ __('menus.cost.implement.agency') }}</span>
                    </a>
                </li>

                <li class="{{ Request::routeIs('cost.implement.program.*') ? 'mm-active' : '' }}">
                    <a href="{{ route('cost.implement.program.index') }}"
                        class="{{ Request::routeIs('cost.implement.program.*') ? 'active' : '' }}">
                        <i data-feather="folder"></i>
                        <span data-key="t-cost.implement.program">{{ __('menus.cost.implement.program') }}</span>
                    </a>
                </li>

                <li class="{{ Request::routeIs('cost.implement.importants.*') ? 'mm-active' : '' }}">
                    <a href="{{ route('cost.implement.importants.index') }}"
                        class="{{ Request::routeIs('cost.implement.importants.*') ? 'active' : '' }}">
                        <i data-feather="folder"></i>
                        <span
                            data-key="t-cost.implement.importants">{{ __('menus.cost.implement.importants') }}</span>
                    </a>
                </li>

                <li class="{{ Request::routeIs('states.assets.vehicles.*') ? 'mm-active' : '' }}">
                    <a href="{{ route('states.assets.vehicles.index') }}"
                        class="{{ Request::routeIs('states.assets.vehicles.*') ? 'active' : '' }}">
                        <i data-feather="folder"></i>
                        <span data-key="t-states.assets.vehicles">{{ __('menus.state.assets.vehicles') }}</span>
                    </a>
                </li>


                {{-- ========== Setting ========== --}}
                <li class="menu-title" data-key="t-content">{{ __('menus.content') }}</li>

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
                            <span>{{ __('menus.content.chapters') }}</span>
                        </a>
                    </li>
                @endif

                @if (hasPermission('ministries.index'))
                    <li class="{{ Request::routeIs('initialProgram.*') ? 'mm-active' : '' }}">
                        <a href="{{ route('initialProgram.index') }}"
                            class="{{ Request::routeIs('initialProgram.*') ? 'active' : '' }}">
                            <i data-feather="layers"></i>
                            <span>{{ __('menus.content.program') }}</span>
                        </a>
                    </li>
                @endif

                @if (hasPermission('ministries.index'))
                    <li class="{{ Request::routeIs('initialAgency.*') ? 'mm-active' : '' }}">
                        <a href="{{ route('initialAgency.index') }}"
                            class="{{ Request::routeIs('initialAgency.*') ? 'active' : '' }}">
                            <i data-feather="layers"></i>
                            <span>{{ __('menus.content.agency') }}</span>
                        </a>
                    </li>
                @endif

                @if (hasPermission('ministries.index'))
                    <li>
                        <a href="{{ route('expenseType.index') }}"
                            class="{{ Request::routeIs('expenseType.*') ? 'active' : '' }}">
                            <i data-feather="layers" title="expense_ty"></i>
                            <span data-key="t-dashboard">{{ __('menus.content.expense.type') }}</span>
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
                @if (hasPermission('role.index') and auth()->user()->role_id != 1)
                    <li class="{{ Request::routeIs('role.*') ? 'mm-active' : '' }}">
                        <a href="{{ route('role.index') }}">
                            <i data-feather="sliders"></i>
                            <span data-key="t-roles">{{ __('menus.setting.roles') }}</span>
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
