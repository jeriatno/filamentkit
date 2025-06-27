 <x-filament::page>
   <div x-data="{ activeTab: 'profile' }">

     <!-- Tab Navigation -->
     <div class="flex justify-center">
       <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg flex p-1">

         <button @click="activeTab = 'profile'" class="px-6 py-1 text-sm font-medium rounded-lg transition-all"
           :style="activeTab === 'profile' ? 'background-color: #F8EDF8; color: #8F348E; font-weight: bold;' : 'color: #AA93AA;'">
           Profile
         </button>

         <button @click="activeTab = 'security'" class="px-6 py-1 text-sm font-medium rounded-lg transition-all"
           :style="activeTab === 'security' ? 'background-color: #F8EDF8; color: #8F348E; font-weight: bold;' : 'color: #AA93AA;'">
           Security
         </button>

       </div>
     </div>

     <!-- Tab Content -->
     <div class="rounded-lg mt-0 pt-0">
       <template x-if="activeTab === 'profile'">
         <div class="space-y-6 divide-y divide-gray-900/10 dark:divide-white/10">
           @foreach ($this->getRegisteredMyProfileComponents() as $component)
             @unless (is_null($component))
               @livewire($component)
             @endunless
           @endforeach
         </div>
       </template>

       <template x-if="activeTab === 'security'">
         <div>
           @livewire('my-security-extended')
         </div>
       </template>
     </div>
   </div>
   <style>
     .divide-y>:not([hidden])~:not([hidden]) {
       --tw-divide-y-reverse: 0;
       border: none !important;
     }
   </style>
 </x-filament::page>

