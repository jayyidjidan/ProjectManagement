<aside
    class="w-90 bg-sidebar border-r border-border flex flex-col h-screen overflow-hidden sticky top-0">

    <div
        class="h-20 px-6 flex items-center">

        <h1
            class="font-bold text-xl">

            Plainthing

        </h1>

    </div>

    @php

        $user = auth()->user();

    @endphp

    <nav
        class="flex-1 px-4 overflow-y-auto">

        <ul
            class="space-y-2">

            {{-- DASHBOARD --}}
            <li>

                <a
                    href="{{ route('dashboard') }}"
                    class="flex items-center px-4 py-3 rounded-2xl hover:bg-[#EFEFEF] transition">

                    Dashboard

                </a>

            </li>

            {{-- PROJECT MANAGEMENT --}}
            @if($user->canManageProjects())

            <li>

                <details open>

                    <summary
                        class="flex items-center justify-between px-4 py-3 rounded-2xl cursor-pointer hover:bg-[#EFEFEF] transition">

                        <span>
                            Project Management
                        </span>

                        <span>
                            ⌄
                        </span>

                    </summary>

                    <ul
                        class="mt-2 ml-4 space-y-1">

                        <li>

                            <a
                                href="{{ route('projects.index') }}"
                                class="block px-4 py-2 rounded-xl hover:bg-[#EFEFEF]">

                                Projects

                            </a>

                        </li>

                        <li>

                            <a
                                href="{{ route('tasks.index') }}"
                                class="block px-4 py-2 rounded-xl hover:bg-[#EFEFEF]">

                                Tasks

                            </a>

                        </li>

                        <li>

                            <a
                                href="{{ route('clients.index') }}"
                                class="block px-4 py-2 rounded-xl hover:bg-[#EFEFEF]">

                                Clients

                            </a>

                        </li>

                    </ul>

                </details>

            </li>

            @endif

            {{-- FINANCE --}}
            @if($user->canManageProjects())

            <li>

                <details>

                    <summary
                        class="flex items-center justify-between px-4 py-3 rounded-2xl cursor-pointer hover:bg-[#EFEFEF] transition">

                        <span>
                            Finance
                        </span>

                        <span>
                            ⌄
                        </span>

                    </summary>

                    <ul
                        class="mt-2 ml-4 space-y-1">

                        <li>

                            <a
                                href="{{ route('payments.index') }}"
                                class="block px-4 py-2 rounded-xl hover:bg-[#EFEFEF]">

                                Payments

                            </a>

                        </li>

                        <li>

                            <a
                                href="{{ route('transactions.index') }}"
                                class="block px-4 py-2 rounded-xl hover:bg-[#EFEFEF]">

                                Transactions

                            </a>

                        </li>

                    </ul>

                </details>

            </li>

            @endif

            {{-- SCRUM --}}
            @if($user->canManageProjects())

            <li>

                <details>

                    <summary
                        class="flex items-center justify-between px-4 py-3 rounded-2xl cursor-pointer hover:bg-[#EFEFEF] transition">

                        <span>
                            Scrum
                        </span>

                        <span>
                            ⌄
                        </span>

                    </summary>

                    <ul
                        class="mt-2 ml-4 space-y-1">

                        <li>

                            <a
                                href="{{ route('scrum.today') }}"
                                class="block px-4 py-2 rounded-xl hover:bg-[#EFEFEF]">

                                Daily Scrum

                            </a>

                        </li>

                        <li>

                            <a
                                href="{{ route('scrums.index') }}"
                                class="block px-4 py-2 rounded-xl hover:bg-[#EFEFEF]">

                                Scrum History

                            </a>

                        </li>

                    </ul>

                </details>

            </li>

            @endif

            {{-- EMPLOYEE --}}
            @if($user->canManageProjects())

            <li>

                <details>

                    <summary
                        class="flex items-center justify-between px-4 py-3 rounded-2xl cursor-pointer hover:bg-[#EFEFEF] transition">

                        <span>
                            Employee
                        </span>

                        <span>
                            ⌄
                        </span>

                    </summary>

                    <ul
                        class="mt-2 ml-4 space-y-1">

                        <li>

                            <a
                                href="{{ route('members.index') }}"
                                class="block px-4 py-2 rounded-xl hover:bg-[#EFEFEF]">

                                Employees

                            </a>

                        </li>

                        <li>

                            <a
                                href="{{ route('attendances.index') }}"
                                class="block px-4 py-2 rounded-xl hover:bg-[#EFEFEF]">

                                Attendance

                            </a>

                        </li>

                        <li>

                            <a
                                href="{{ route('overtimes.approvals') }}"
                                class="block px-4 py-2 rounded-xl hover:bg-[#EFEFEF]">

                                Overtime Approvals

                            </a>

                        </li>

                        @if($user->isSuperAdmin())

                        <li>

                            <a
                                href="{{ route('invitations.index') }}"
                                class="block px-4 py-2 rounded-xl hover:bg-[#EFEFEF]">

                                Invitations

                            </a>

                        </li>

                        <li>

                            <a
                                href="{{ route('overtimes.index') }}"
                                class="block px-4 py-2 rounded-xl hover:bg-[#EFEFEF]">

                                Overtime 

                            </a>

                        </li>

                        @endif

                    </ul>

                </details>

            </li>

            @endif

            {{-- EMPLOYEE FEATURE --}}
            @if($user->isEmployee())

            <li>

                <a
                    href="{{ route('checkin.index') }}"
                    class="flex items-center px-4 py-3 rounded-2xl hover:bg-[#EFEFEF] transition">

                    Check In

                </a>

            </li>

            <li>

                <a
                    href="{{ route('overtimes.index') }}"
                    class="flex items-center px-4 py-3 rounded-2xl hover:bg-[#EFEFEF] transition">

                    Overtime

                </a>

            </li>

            <li>

                <a
                    href="{{ route('my-attendance.index') }}"
                    class="flex items-center px-4 py-3 rounded-2xl hover:bg-[#EFEFEF] transition">

                    My Attendance

                </a>

            </li>

            <li>

                <a
                    href="{{ route('overtimes.history') }}"
                    class="flex items-center px-4 py-3 rounded-2xl hover:bg-[#EFEFEF] transition">

                    My Overtime

                </a>

            </li>

            <li>

                <a
                    href="{{ route('my-task.index') }}"
                    class="flex items-center px-4 py-3 rounded-2xl hover:bg-[#EFEFEF] transition">

                    My Task

                </a>

            </li>

            <li>
                <a
                    href="{{ route('chat') }}"
                    class=" flex items-center px-4 py-3 rounded-2xl hover:bg-[#EFEFEF] transition {{ request()->routeIs('chat') ? 'active-menu' : '' }}">

                    AI Assistant

                </a>
            </li>

            
            @endif

            {{-- WORK HOURS --}}
            @if($user->canManageSystem())

            <li>

                <a
                    href="{{ route('work-settings.index') }}"
                    class="flex items-center px-4 py-3 rounded-2xl hover:bg-[#EFEFEF] transition">

                    Work Hours

                </a>

            </li>

            @endif

            {{-- MASTER DATA --}}
            @if($user->canManageSystem())

            <li>

                <details>

                    <summary
                        class="flex items-center justify-between px-4 py-3 rounded-2xl cursor-pointer hover:bg-[#EFEFEF] transition">

                        <span>
                            Master Data
                        </span>

                        <span>
                            ⌄
                        </span>

                    </summary>

                    <ul
                        class="mt-2 ml-4 space-y-1">

                        <li><a href="{{ route('roles.index') }}" class="block px-4 py-2 rounded-xl hover:bg-[#EFEFEF]">Roles</a></li>

                        <li><a href="{{ route('jabatans.index') }}" class="block px-4 py-2 rounded-xl hover:bg-[#EFEFEF]">Positions</a></li>

                        <li><a href="{{ route('status-members.index') }}" class="block px-4 py-2 rounded-xl hover:bg-[#EFEFEF]">Member Status</a></li>

                        <li><a href="{{ route('status-proyeks.index') }}" class="block px-4 py-2 rounded-xl hover:bg-[#EFEFEF]">Project Status</a></li>

                        <li><a href="{{ route('status-tasks.index') }}" class="block px-4 py-2 rounded-xl hover:bg-[#EFEFEF]">Task Status</a></li>

                        <li><a href="{{ route('priorities.index') }}" class="block px-4 py-2 rounded-xl hover:bg-[#EFEFEF]">Priorities</a></li>

                        <li><a href="{{ route('tipes.index') }}" class="block px-4 py-2 rounded-xl hover:bg-[#EFEFEF]">Project Types</a></li>

                        <li><a href="{{ route('kategoris.index') }}" class="block px-4 py-2 rounded-xl hover:bg-[#EFEFEF]">Categories</a></li>

                        <li><a href="{{ route('sumber-kliens.index') }}" class="block px-4 py-2 rounded-xl hover:bg-[#EFEFEF]">Client Sources</a></li>

                        <li><a href="{{ route('jenis-transaksis.index') }}" class="block px-4 py-2 rounded-xl hover:bg-[#EFEFEF]">Transaction Types</a></li>

                        <li><a href="{{ route('skills.index') }}" class="block px-4 py-2 rounded-xl hover:bg-[#EFEFEF]">Skills</a></li>

                    </ul>

                </details>

            </li>


            @endif

        </ul>

    </nav>

</aside>