<div
    x-data="{
        show: false,
        title: '',
        message: '',
        type: 'danger',
        confirmText: 'Confirm',
        onConfirm: null,
        open(data) {
            this.title = data.title || 'Are you sure?'
            this.message = data.message || ''
            this.type = data.type || 'danger'
            this.confirmText = data.confirmText || 'Confirm'
            this.onConfirm = data.onConfirm
            this.show = true
        },
        confirm() {
            if (this.onConfirm) this.onConfirm()
            this.show = false
        },
        cancel() {
            this.show = false
        }
    }"
    @confirm-modal.window="open($event.detail)"
    x-show="show"
    x-cloak
    class="fixed inset-0 z-100 overflow-y-auto"
>
    <!-- Backdrop -->
    <div
        x-show="show"
        x-transition:enter="ease-out duration-200"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="ease-in duration-150"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed inset-0 bg-lilac-950/30"
        @click="cancel()"
    ></div>

    <!-- Modal -->
    <div class="fixed inset-0 flex items-center justify-center p-4">
        <div
            x-show="show"
            x-transition:enter="ease-out duration-200"
            x-transition:enter-start="opacity-0 scale-95"
            x-transition:enter-end="opacity-100 scale-100"
            x-transition:leave="ease-in duration-150"
            x-transition:leave-start="opacity-100 scale-100"
            x-transition:leave-end="opacity-0 scale-95"
            class="bg-white rounded-xl shadow-xl max-w-md w-full p-6"
            @click.stop
        >
            <h3 class="text-sm font-display font-semibold text-lilac-950 mb-2" x-text="title"></h3>
            <p class="text-sm text-gray-500 mb-6" x-text="message" x-show="message"></p>
            
            <div class="flex items-center justify-end gap-3">
                <button
                    @click="cancel()"
                    type="button"
                    class="text-sm inline-flex items-center justify-center gap-2 rounded-md cursor-pointer transition-colors focus:outline-hidden focus:ring-2 focus:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed bg-lilac-50 text-gray-700 hover:bg-gray-200 focus:ring-lilac-500 px-4 py-2"
                >
                    Cancel
                </button>
                <button
                    @click="confirm()"
                    type="button"
                    class="text-sm inline-flex items-center justify-center gap-2 rounded-md cursor-pointer transition-colors focus:outline-hidden focus:ring-2 focus:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed px-4 py-2"
                    :class="{
                        'bg-red-600 text-white hover:bg-red-500 focus:ring-red-500': type === 'danger',
                        'bg-lilac-600 text-white hover:bg-lilac-700 focus:ring-lilac-500': type !== 'danger'
                    }"
                    x-text="confirmText"
                >
                </button>
            </div>
        </div>
    </div>
</div>
