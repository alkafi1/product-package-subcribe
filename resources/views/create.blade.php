@extends('layout')

@section('main-content')
    <div class="container">
        <h1>Create Product</h1>

        @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('products.store') }}" method="POST">
            @csrf

            <div class="mb-3">
                <label for="name" class="form-label">Product Name</label>
                <input type="text" class="form-control" id="name" name="name">
            </div>
            <div class="mb-3">
                <label for="name" class="form-label">Product Slug</label>
                <input type="text" class="form-control" id="slug" name="slug">
            </div>

            <div class="mb-3">
                <label for="price" class="form-label">Price</label>
                <input type="number" step="0.01" class="form-control" id="price" name="price">
            </div>

            <div class="mb-3">
                <label for="quantity" class="form-label">Quantity</label>
                <input type="number" class="form-control" id="quantity" name="quantity">
            </div>

            <!-- Is Bundle Checkbox -->
            <div class="mb-3 form-check">
                <input type="checkbox" class="form-check-input" id="is_bundle" name="is_bundle">
                <label class="form-check-label" for="is_bundle">Is Bundle</label>
            </div>

            <div id="bundle_details_container" style="display: none;">
                <h4>Bundle Details</h4>
                <div class="bundle-group">
                    <!-- Initial Bundle Detail (no remove button) -->
                    <div class="bundle_detail card-border-green mb-3 p-3 border border-success rounded">
                        <div class="mb-3">
                            <label for="bundle_quantity" class="form-label">Bundle Quantity</label>
                            <input type="number" class="form-control" name="bundle_quantity[]">
                        </div>
                        <div class="mb-3">
                            <label for="bundle_discount_type" class="form-label">Discount Type</label>
                            <select class="form-select" name="bundle_discount_type[]">
                                <option value="percentage">Percentage</option>
                                <option value="fixed">Fixed</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="bundle_discount_amount" class="form-label">Discount Amount</label>
                            <input type="number" class="form-control" name="bundle_discount_amount[]">
                        </div>
                    </div>
                </div>
                <button type="button" id="add_bundle" class="btn btn-success">
                    <i class="fas fa-add"></i> Add More Bundle
                </button>
            </div>

            <!-- Is Subscribable Checkbox -->
            <div class="mb-3 form-check">
                <input type="checkbox" class="form-check-input" id="is_subscribable" name="is_subscribable">
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
                    <!-- Initial Schedule Detail (no remove button) -->
                    <div class="schedule_detail card-border-green mb-3 p-3 border border-success rounded">

                        <div class="mb-3">
                            <label for="schedule_interval" class="form-label">Interval</label>
                            <select class="form-select schedule_interval" name="schedule_interval[]">
                                <!-- Options will be set via JavaScript -->
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="schedule_day" class="form-label">Day of Week (0 = Sunday)</label>
                            <input type="number" class="form-control" name="schedule_day[]" min="0" max="6">
                        </div>
                        <div class="mb-3">
                            <label for="schedule_time" class="form-label">Time</label>
                            <input type="time" class="form-control" name="schedule_time[]">
                        </div>
                    </div>
                </div>
                <button type="button" id="add_schedule" class="btn btn-success">
                    <i class="fas fa-add"></i> Add More Schedule
                </button>
            </div>

            <button type="submit" class="btn btn-primary mt-2">Create Product</button>
        </form>
    </div>

    <!-- jQuery and Script for Dynamic Handling -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
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

    <script>
        @if (session('success'))
            Swal.fire({
                icon: 'success',
                title: 'Success!',
                text: '{{ session('success') }}',
                timer: 3000, // Automatically close after 3 seconds
                showConfirmButton: false
            });
        @endif
    </script>
@endsection
