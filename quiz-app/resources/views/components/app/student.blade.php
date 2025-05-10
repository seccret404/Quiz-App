<header class="sticky top-0 before:absolute before:inset-0 before:backdrop-blur-md max-lg:before:bg-[#1111112D] dark:max-lg:before:bg-[#1111112D] before:-z-10 z-30 {{ $variant === 'v2' || $variant === 'v3' ? 'before:bg-[#1111112D] after:absolute after:h-px after:inset-x-0 after:top-full after:bg-[#1111112D] dark:after:bg-[#1111112D] after:-z-10' : 'max-lg:shadow-xs lg:before:bg-[#1111112D] dark:lg:before:bg-[#1111112D]' }} {{ $variant === 'v2' ? 'dark:before:bg-[#1111112D]' : '' }} {{ $variant === 'v3' ? 'dark:before:bg-white' : '' }}">
    <div class="px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-16 {{ $variant === 'v2' || $variant === 'v3' ? '' : ' ' }}">

            <!-- Header: Left side -->
            <div class="flex">

                <!-- Hamburger button -->
                <button
                    class="text-gray-500 hover:text-gray-600 dark:hover:text-gray-400 lg:hidden"
                    @click.stop="sidebarOpen = !sidebarOpen"
                    aria-controls="sidebar"
                    :aria-expanded="sidebarOpen"
                >
                    <span class="sr-only">Open sidebar</span>
                    <svg class="w-6 h-6 fill-current" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <rect x="4" y="5" width="16" height="2" />
                        <rect x="4" y="11" width="16" height="2" />
                        <rect x="4" y="17" width="16" height="2" />
                    </svg>
                </button>

            </div>

            <!-- Header: Right side -->
            <div class="flex items-center space-x-3">
                <form action="{{route('logout')}}" method="post">
                    @csrf
                    <button type="submit" class="cursor-pointer rounded p-1 bg-[#EE0404] text-[#FFFFFF]" >Sign Out</button>
                </form>
                <hr class="w-px h-6 bg-gray-200 dark:bg-gray-700/60 border-none" />
                @php
                    $user = session('user');
                @endphp
                <button class="bg-[#26107459] text-white p-4 rounded cursor-pointer" onclick="window.location.href='{{ route('my.dashboard') }}'">
                      {{ $user['name'] ?? 'Dashboard' }}'s Dashboard
                </button>
            </div>

        </div>
    </div>
</header>
