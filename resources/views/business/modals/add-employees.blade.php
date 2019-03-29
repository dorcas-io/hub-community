<div id="add-employees" class="modal">
    <form class="col s12" action="" method="post">
        {{ csrf_field() }}
        <div class="modal-content">
            <h4>Add Employees</h4>
            <div class="row">
                <div class="col s12">
                    @if (!empty($employees) && $employees->count() > 0)
                        <div class="row">
                            <div class="input-field col s12">
                                <select name="employees[]" id="employees" multiple>
                                    <option value="" disabled>Select Employees</option>
                                    @foreach ($employees as $employee)
                                        <option value="{{ $employee->id }}">{{ $employee->firstname . ' ' . $employee->lastname }}</option>
                                    @endforeach
                                </select>
                                <label for="employees" @if ($errors->has('employees')) data-error="{{ $errors->first('employees') }}" @endif>Select Employees</label>
                            </div>
                            <p class="flow-text">&nbsp;</p>
                        </div>
                    @else
                        @component('layouts.slots.empty-fullpage')
                            @slot('icon')
                                people_outline
                            @endslot
                            {{ $noEmployeesMessage }}
                            @slot('buttons')
                                <a class="btn-flat blue darken-3 white-text waves-effect waves-light" href="{{ route('business.employees.new') }}">
                                    Add Employees
                                </a>
                            @endslot
                        @endcomponent
                    @endif
                </div>
            </div>
        </div>
        <div class="modal-footer" v-if="showAddButton">
            <div class="progress" v-if="updating">
                <div class="indeterminate"></div>
            </div>
            <button type="submit" class="modal-action waves-effect waves-green btn-flat" name="add_employees"
                    value="1" v-if="!updating">Add Employee(s)</button>
        </div>
    </form>
</div>