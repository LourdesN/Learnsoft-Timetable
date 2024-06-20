<!-- need to remove -->
<li class="nav-item">
    <a href="{{ route('home') }}" class="nav-link {{ Request::is('home') ? 'active' : '' }}">
        <i class="nav-icon fas fa-home"></i>
        <p>Home</p>
    </a>
</li>
<li class="nav-item">
    <a href="{{ route('timetable') }}" class="nav-link {{ Request::is('timetables') ? 'active' : '' }}">
    <i class=" fas fa-solid fa-table"></i>
        <p>Timetable</p>
    </a>
</li>


<li class="nav-item">
    <a href="{{ route('grades.index') }}" class="nav-link {{ Request::is('grades*') ? 'active' : '' }}">
    <i class="fas fa-solid fa-school"></i>
        <p>Grades</p>
    </a>
</li>

<li class="nav-item">
    <a href="{{ route('schedules.index') }}" class="nav-link {{ Request::is('schedules*') ? 'active' : '' }}">
    <i class="fas fa-regular fa-clock"></i> 
        <p>Schedule</p>
    </a>
</li>
<li class="nav-item">
    <a href="{{ route('breaks.index') }}" class="nav-link {{ Request::is('breaks*') ? 'active' : '' }}">
    <i class="fas fa-solid fa-bread-slice"></i>
        <p>Breaks</p>
    </a>
</li>
<li class="nav-item">
    <a href="{{ route('teachers_learning_areas.index') }}" class="nav-link {{ Request::is('teachers_learning_areas*') ? 'active' : '' }}">
    <i class="fas fa-solid fa-chalkboard"></i>
        <p>TeachersLearningAreas</p>
    </a>
</li>
