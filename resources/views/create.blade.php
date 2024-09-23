@extends('layout')

@section('main-content')
    <div class="container mt-2">
        <!-- Animated Background Section -->
    
        <h1>Create Product</h1>

        {{-- @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif --}}

        <form action="{{ route('products.store') }}" method="POST">
            @csrf

            <!-- Product Name -->
            <div class="mb-3">
                <label for="name" class="form-label">Product Name</label>
                <input type="text" class="form-control {{ $errors->has('name') ? 'is-invalid blink' : '' }}" id="name"
                    name="name" value="{{ old('name') }}">
                
            </div>

            <!-- Product Slug -->
            <div class="mb-3">
                <label for="slug" class="form-label">Product Slug</label>
                <input type="text" class="form-control {{ $errors->has('slug') ? 'is-invalid blink' : '' }}"
                    id="slug" name="slug" value="{{ old('slug') }}">
                
            </div>

            <!-- Price -->
            <div class="mb-3">
                <label for="price" class="form-label">Price</label>
                <input type="number" step="0.01"
                    class="form-control {{ $errors->has('price') ? 'is-invalid blink' : '' }}" id="price" name="price"
                    value="{{ old('price') }}">
                
            </div>

            <!-- Quantity -->
            <div class="mb-3">
                <label for="quantity" class="form-label">Quantity</label>
                <input type="number" class="form-control {{ $errors->has('quantity') ? 'is-invalid blink' : '' }}"
                    id="quantity" name="quantity" value="{{ old('quantity') }}">
                
            </div>
            <!-- Is Bundle Checkbox -->
            <div class="mb-3 form-check">
                <input type="checkbox" class="form-check-input {{ $errors->has('is_bundle') ? 'is-invalid blink' : '' }}"
                    id="is_bundle" name="is_bundle" {{ old('is_bundle') ? 'checked' : '' }}>
                <label class="form-check-label" for="is_bundle">Is Bundle</label>
                
            </div>
            <div id="bundle_details_container" style="display: none;">
                <h4>Bundle Details</h4>
                <div class="bundle-group">
                    @php
                        $bundle_quantities = old('bundle_quantity', []);
                        $bundle_discount_types = old('bundle_discount_type', []);
                        $bundle_discount_amounts = old('bundle_discount_amount', []);
                    @endphp

                    @if (count($bundle_quantities) > 0)
                        @foreach ($bundle_quantities as $index => $quantity)
                            <div class="bundle_detail card-border-green mb-3 p-3 border border-success rounded">
                                <div class="mb-3">
                                    <label for="bundle_quantity_{{ $index }}" class="form-label">Bundle
                                        Quantity</label>
                                    <input type="number"
                                        class="form-control @error("bundle_quantity.{$index}") is-invalid @enderror"
                                        name="bundle_quantity[]" value="{{ $quantity }}">
                                    
                                </div>
                                <div class="mb-3">
                                    <label for="bundle_discount_type_{{ $index }}" class="form-label">Discount
                                        Type</label>
                                    <select class="form-select @error("bundle_discount_type.{$index}") is-invalid @enderror"
                                        name="bundle_discount_type[]">
                                        <option value="percentage"
                                            {{ old("bundle_discount_type.{$index}") == 'percentage' ? 'selected' : '' }}>
                                            Percentage</option>
                                        <option value="fixed"
                                            {{ old("bundle_discount_type.{$index}") == 'fixed' ? 'selected' : '' }}>Fixed
                                        </option>
                                    </select>
                                    
                                </div>
                                <div class="mb-3">
                                    <label for="bundle_discount_amount_{{ $index }}" class="form-label">Discount
                                        Amount</label>
                                    <input type="number"
                                        class="form-control @error("bundle_discount_amount.{$index}") is-invalid @enderror"
                                        name="bundle_discount_amount[]"
                                        value="{{ old("bundle_discount_amount.{$index}") }}">

                                </div>
                            </div>
                        @endforeach
                    @else
                        <!-- Initial empty bundle detail -->
                        <div class="bundle_detail card-border-green mb-3 p-3 border border-success rounded">
                            <div class="mb-3">
                                <label class="form-label">Bundle Quantity</label>
                                <input type="number" class="form-control" name="bundle_quantity[]">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Discount Type</label>
                                <select class="form-select" name="bundle_discount_type[]">
                                    <option value="percentage">Percentage</option>
                                    <option value="fixed">Fixed</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Discount Amount</label>
                                <input type="number" class="form-control" name="bundle_discount_amount[]">
                            </div>
                        </div>
                    @endif
                </div>
                <button type="button" id="add_bundle" class="btn btn-success">
                    <i class="fas fa-add"></i> Add More
                </button>
            </div>

            <!-- Subscribable Checkbox -->
            <div class="mb-3 form-check">
                <input type="checkbox"
                    class="form-check-input {{ $errors->has('is_subscribable') ? 'is-invalid blink' : '' }}"
                    id="is_subscribable" name="is_subscribable" {{ old('is_subscribable') ? 'checked' : '' }}>
                <label class="form-check-label" for="is_subscribable">Is Subscribable</label>

            </div>

            <div id="schedule_details_container" style="display: none;">
                <h4>Schedule Details</h4>
                <div class="mb-3">
                    <label for="schedule_type" class="form-label">Schedule Type</label>
                    <select class="form-select schedule_type" name="schedule_type">
                        <option value="monthly">Monthly</option>
                        <option value="days">Days</option>
                    </select>
                </div>
                <div class="schedule-group">
                    @php
                        $schedule_intervals = old('schedule_interval', []);
                        $schedule_days = old('schedule_day', []);
                        $schedule_times = old('schedule_time', []);
                    @endphp

                    @if (count($schedule_intervals) > 0)
                        @foreach ($schedule_intervals as $index => $interval)
                            <div class="schedule_detail card-border-green mb-3 p-3 border border-success rounded">
                                <div class="mb-3">
                                    <label for="schedule_interval_{{ $index }}"
                                        class="form-label">Interval</label>
                                    <select
                                        class="form-select schedule_interval @error("schedule_interval.{$index}") is-invalid @enderror"
                                        name="schedule_interval[]">
                                        <!-- Options populated by JS -->
                                    </select>
                                </div>

                                <div class="mb-3">
                                    <label for="schedule_day_{{ $index }}" class="form-label">Day of Week (0 =
                                        Sunday)</label>
                                    <input type="number"
                                        class="form-control @error("schedule_day.{$index}") is-invalid @enderror"
                                        name="schedule_day[]" min="0" max="6"
                                        value="{{ old("schedule_day.{$index}") }}">
                                </div>

                                <div class="mb-3">
                                    <label for="schedule_time_{{ $index }}" class="form-label">Time</label>
                                    <input type="time"
                                        class="form-control @error("schedule_time.{$index}") is-invalid @enderror"
                                        name="schedule_time[]" value="{{ old("schedule_time.{$index}") }}">

                                </div>
                            </div>
                        @endforeach
                    @else
                        <!-- Initial empty schedule detail -->
                        <div class="schedule_detail card-border-green mb-3 p-3 border border-success rounded">
                            <div class="mb-3">
                                <label for="schedule_interval_0" class="form-label">Interval</label>
                                <select class="form-select schedule_interval" name="schedule_interval[]">
                                    <!-- Options populated by JS -->
                                </select>
                            </div>

                            <div class="mb-3">
                                <label for="schedule_day_0" class="form-label">Day of Week (0 = Sunday)</label>
                                <input type="number" class="form-control" name="schedule_day[]" min="0"
                                    max="6">
                            </div>

                            <div class="mb-3">
                                <label for="schedule_time_0" class="form-label">Time</label>
                                <input type="time" class="form-control" name="schedule_time[]">
                            </div>
                        </div>
                    @endif
                </div>
                <button type="button" id="add_schedule" class="btn btn-success">
                    <i class="fas fa-add"></i> Add More
                </button>
            </div>

            <button type="submit" class="btn btn-dark bg-dark mt-2">Create Product</button>
        </form>
    </div>
@push('script')
    <!-- jQuery and Script for Dynamic Handling -->
    <script>
        // Error blink effect
        function blinkError(element) {
            $(element).fadeOut(100).fadeIn(100).fadeOut(100).fadeIn(100);
        }

        // Check if there are any errors on load
        @if ($errors->any())
            $('.is-invalid').each(function() {
                blinkError(this);
            });
        @endif
        // Bundle Details Toggle
        $('#is_bundle').change(function() {
            if ($(this).is(':checked')) {
                $('#bundle_details_container').show();
            } else {
                $('#bundle_details_container').hide();
            }
        });

        // Subscribable Schedule Details Toggle
        $('#is_subscribable').change(function() {
            if ($(this).is(':checked')) {
                $('#schedule_details_container').show();
            } else {
                $('#schedule_details_container').hide();
            }
        });

        // Add more bundles
        $('#add_bundle').click(function() {
            const bundleDetailTemplate = `
                <div class="bundle_detail card-border-green mb-3 p-3 border border-success rounded">
                    <div class="mb-3">
                        <label class="form-label">Bundle Quantity</label>
                        <input type="number" class="form-control" name="bundle_quantity[]">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Discount Type</label>
                        <select class="form-select" name="bundle_discount_type[]">
                            <option value="percentage">Percentage</option>
                            <option value="fixed">Fixed</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Discount Amount</label>
                        <input type="number" class="form-control" name="bundle_discount_amount[]">
                    </div>
                    <button type="button" class="btn btn-danger remove-bundle">
                        <i class="fas fa-trash-alt"></i> Remove
                    </button>
                </div>`;
            $('.bundle-group').append(bundleDetailTemplate);
        });

        // Remove bundle
        $(document).on('click', '.remove-bundle', function() {
            $(this).closest('.bundle_detail').remove();
        });

        // Function to populate the interval options based on schedule type
        function populateInterval(selectElement, scheduleType) {
            selectElement.empty(); // Clear existing options

            if (scheduleType === 'monthly') {
                const months = [1, 2, 6]; // 1 to 6 months
                months.forEach(month => {
                    selectElement.append(
                        `<option value="${month}">${month} month${month > 1 ? 's' : ''}</option>`
                    );
                });
            } else if (scheduleType === 'days') {
                const days = [7, 14]; // 7 and 14 days
                days.forEach(day => {
                    selectElement.append(
                        `<option value="${day}">${day} day${day > 1 ? 's' : ''}</option>`
                    );
                });
            }
        }

        // Populate interval for the initial schedule block based on the selected type
        const initialScheduleType = $('.schedule_type').val();
        populateInterval($('.schedule_interval'), initialScheduleType);

        // Add more schedules dynamically (without Schedule Type)
        $('#add_schedule').click(function() {
            const scheduleDetailTemplate = `
            <div class="schedule_detail card-border-green mb-3 p-3 border border-success rounded">
                <div class="mb-3">
                    <label for="schedule_interval_new" class="form-label">Interval</label>
                    <select class="form-select schedule_interval" name="schedule_interval[]" >
                        <!-- Options will be set via JavaScript -->
                    </select>
                </div>
                <div class="mb-3">
                    <label for="schedule_day_new" class="form-label">Day of Week (0 = Sunday)</label>
                    <input id="schedule_day_new" type="number" class="form-control" name="schedule_day[]" min="0" max="6" >
                </div>
                <div class="mb-3">
                    <label for="schedule_time_new" class="form-label">Time</label>
                    <input id="schedule_time_new" type="time" class="form-control" name="schedule_time[]">
                </div>
                <button type="button" class="btn btn-danger remove-schedule">
                    <i class="fas fa-trash-alt"></i> Remove
                </button>
            </div>`;

            $('.schedule-group').append(scheduleDetailTemplate); // Add the new schedule block

            // Automatically populate interval for the new schedule based on the initial schedule type
            const initialScheduleType = $('.schedule_type').val();
            populateInterval($('.schedule_interval'), initialScheduleType);
        });

        // Handle schedule type changes for the initial block
        $(document).on('change', '.schedule_type', function() {
            const scheduleType = $(this).val(); // Get selected schedule type
            const intervalSelect = $('.schedule_interval');
            populateInterval(intervalSelect, scheduleType); // Update interval based on type
        });

        // Remove schedule
        $(document).on('click', '.remove-schedule', function() {
            $(this).closest('.schedule_detail').remove();
        });
    </script>

@endpush
    


@endsection
