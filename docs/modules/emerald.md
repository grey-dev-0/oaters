# Emerald Module

## Overview

**Emerald** is the project and task management module of the OATERS ERP system. It provides comprehensive functionality for planning, organizing, and tracking work through milestones, tasks, assignments, and collaboration features. Emerald enables teams to manage complex projects with detailed task hierarchies, progress tracking, resource allocation, and real-time collaboration through comments and attachments.

## Business Functionality

### Core Features

#### 1. Project Planning & Organization
- **Milestones**: Define project phases and key checkpoints
- **Milestone Dates**: Set start and end dates for project phases
- **Hierarchical Tasks**: Create nested task structures with parent-child relationships
- **Task Organization**: Group and organize work by milestones
- **Time Estimation**: Estimate task duration for capacity planning

#### 2. Task Management
- **Task Creation**: Create tasks with detailed descriptions and tracking
- **Task Hierarchy**: Support for parent-child task relationships (subtasks)
- **Task Status**: Track task progress and completion
- **Task Assignments**: Assign tasks to team members
- **Multiple Assignees**: Support multiple users per task
- **Estimated Time**: Define expected task duration

#### 3. Team Collaboration
- **Comments**: Add discussions and notes to tasks
- **Comment History**: Track all task discussions
- **File Attachments**: Attach and version documents
- **Attachment Versions**: Track file revisions and history
- **User Comments**: Identify who commented and when

#### 4. Task Labeling & Workflow
- **Custom Labels**: Define and apply task labels
- **Label Functions**: Support different label types (workflow status, task type, tags)
- **System Labels**: Pre-defined system labels for common workflows
- **Workflow Definitions**: Define task workflow states
- **Multi-language Labels**: Localized label support

#### 5. Resource & Schedule Management
- **Task Assignees**: Assign contacts to tasks
- **Custom Schedules**: Define working hours and days per person
- **User Schedules**: Track individual work availability
- **Capacity Planning**: Understand team capacity

## Technical Architecture

### Technology Stack

- **Framework**: Laravel 10+
- **Module System**: nwidart/laravel-modules (^10.0)
- **Localization**: astrotomic/laravel-translatable (^11.12)
- **Database**: MySQL/PostgreSQL (tenant-specific)
- **Authentication**: Integrated with Sapphire module
- **Multi-tenancy**: Tenant-scoped data via Sapphire module

## Database Schema

### Core Models

**Milestone Model** (`Milestone.php`)

```php
// Project phases and checkpoints
// Table: e_milestones
```

Schema:
- id: Primary key (increments)
- user_id: Foreign key to users (milestone creator/owner)
- title: Milestone name (string)
- description: Detailed description (text, nullable)
- starts_at: Milestone start date (date)
- ends_at: Milestone end date (date)
- created_at, updated_at: Timestamps

Relationships:
- `user()`: belongsTo(User) from Sapphire module
- `tasks()`: hasMany(Task)

**Task Model** (`Task.php`)

```php
// Project tasks and work items
// Table: e_tasks
```

Schema:
- id: Primary key (increments)
- parent_id: Foreign key to parent task (nullable - for subtasks)
- creator_id: Foreign key to users (task creator)
- milestone_id: Foreign key to milestones (nullable - task may not have milestone)
- estimated_time: Estimated task duration in hours (integer, nullable)
- title: Task name (string)
- description: Task description (text, nullable)
- created_at, updated_at: Timestamps

Relationships:
- `parent()`: belongsTo(Task, 'parent_id') - parent task
- `children()`: hasMany(Task, 'parent_id') - subtasks
- `creator()`: belongsTo(User, 'creator_id')
- `milestone()`: belongsTo(Milestone)
- `assignees()`: belongsToMany(Contact) via e_assignees pivot
- `comments()`: hasMany(Comment)
- `attachments()`: hasMany(Attachment)
- `labels()`: belongsToMany(Label) via e_task_labels

Features:
- Supports hierarchical task structure (nested subtasks)
- Can be organized by milestone or standalone
- Multiple team members can be assigned

**Comment Model** (`Comment.php`)

```php
// Task discussions and collaboration
// Table: e_comments
```

Schema:
- id: Primary key (increments)
- task_id: Foreign key to tasks (nullable)
- user_id: Foreign key to users (comment author, nullable - user may be deleted)
- content: Comment text (text field)
- created_at, updated_at: Timestamps

Relationships:
- `task()`: belongsTo(Task)
- `user()`: belongsTo(User)

Features:
- Comments track discussion on tasks
- User reference preserved even if user account deleted

**Attachment Model** (`Attachment.php`)

```php
// Files attached to tasks
// Table: e_attachments
```

Schema:
- id: Primary key (increments)
- user_id: Foreign key to users (who uploaded file)
- task_id: Foreign key to tasks (nullable - attachment may be standalone)
- filename: Original uploaded filename (string)

Relationships:
- `user()`: belongsTo(User)
- `task()`: belongsTo(Task)
- `versions()`: hasMany(AttachmentVersion)

**AttachmentVersion Model** (`AttachmentVersion.php`)

```php
// File revision tracking
// Table: e_attachment_versions
```

Schema:
- id: Primary key (increments)
- attachment_id: Foreign key to attachments
- created_at: When version was created (timestamp, nullable)

Relationships:
- `attachment()`: belongsTo(Attachment)

Features:
- Tracks each version/revision of a file
- Created_at timestamp records when version was added

**Label Model** (`Label.php`)

```php
// Task labels and workflow definitions
// Table: e_labels
```

Schema:
- id: Primary key (increments)
- function: Label type/purpose (enum: 'type', 'workflow', 'tag')
- system: Whether this is a system label (boolean, default false)
- created_at, updated_at: Timestamps

Translated Attributes (via e_label_locales):
- title: Localized label name

Relationships:
- `translations()`: hasMany(LabelLocale) via Astrotomic Translatable
- `tasks()`: belongsToMany(Task) via e_task_labels
- `workflow()`: hasOne(Workflow) - if this label is a workflow

Features:
- Function defines label purpose (status type, workflow state, or generic tag)
- System labels are pre-defined and cannot be modified

**LabelLocale Model** (`LabelLocale.php`)

```php
// Localized label information
// Table: e_label_locales
```

Schema:
- id: Primary key (increments)
- label_id: Foreign key to labels
- title: Translated label name (string)
- locale: Language code (2 chars)

Relationships:
- `label()`: belongsTo(Label)

**Workflow Model** (`Workflow.php`)

```php
// Workflow state definitions and transitions
// Table: e_workflows
// Extends Label with workflow-specific functionality
```

Schema:
- id: Primary key (same as parent Label - single table inheritance pattern)
- system: Whether workflow is system-defined (boolean, default false)
- label_ids: JSON array of allowed next states (text)

Relationships:
- Inherits all Label relationships
- Links to related labels representing workflow states

Features:
- Defines workflow state machine
- Tracks allowed state transitions
- Can be system-defined or custom

**Assignee Model** (`Assignee.php` - via pivot)

```php
// Task assignments
// Table: e_assignees (pivot table)
```

Schema:
- contact_id: Part of composite primary key
- task_id: Part of composite primary key
- Foreign keys to contacts and tasks

Relationships:
- Pivot for belongsToMany between Task and Contact

**CustomSchedule Model** (`CustomSchedule.php`)

```php
// Individual work schedules
// Table: e_custom_schedules
// Extends Contact with schedule information
```

Schema:
- id: Primary key (references lc_contacts.id - shared primary key)
- working_days: JSON array of working days (0-6, where 0=Sunday)
- start_time: Working hours start time (HH:MM format, 5 chars)
- end_time: Working hours end time (HH:MM format, 5 chars)

Relationships:
- Foreign key to contact (via id)
- `contact()`: belongsTo(Contact)

Features:
- Defines custom working schedule per person
- Specifies which days person works and their working hours
- Enables capacity planning and availability tracking

## Directory Structure

```
Modules/Emerald/
├── App/
│   ├── Http/
│   │   └── Controllers/
│   │       ├── TaskController.php
│   │       ├── MilestoneController.php
│   │       ├── CommentController.php
│   │       ├── AttachmentController.php
│   │       └── [Other project management controllers]
│   ├── Models/                   # Eloquent models (11 models)
│   │   ├── Milestone.php
│   │   ├── Task.php
│   │   ├── Comment.php
│   │   ├── Attachment.php
│   │   ├── AttachmentVersion.php
│   │   ├── Label.php
│   │   ├── LabelLocale.php
│   │   ├── Workflow.php
│   │   ├── Assignee.php
│   │   └── CustomSchedule.php
│   └── Providers/
│       └── ModuleServiceProvider.php
├── Database/
│   ├── Factories/               # Model factories for testing
│   ├── migrations/              # Database migrations
│   │   └── 2024_04_02_104036_emerald_base.php
│   └── Seeders/                # Database seeders
├── resources/
│   ├── views/                  # Blade templates
│   │   ├── tasks/
│   │   ├── milestones/
│   │   └── projects/
│   └── lang/                   # Translations
│       └── en/
├── routes/
│   ├── web.php                 # Web routes
│   └── api.php                 # API routes
├── config/
│   └── config.php              # Module configuration
└── module.json                 # Module metadata
```

## Key Relationships

```
User (Sapphire) (1) ─────── (N) Milestones
User (1) ─────── (N) Tasks (as creator)
User (1) ─────── (N) Comments
User (1) ─────── (N) Attachments

Milestone (1) ─────── (N) Tasks
Milestone (1) ─────── (N) MilestoneLocale

Task (1) ─────── (N) Tasks (parent-child hierarchy)
Task (1) ─────── (N) Comments
Task (1) ─────── (N) Attachments
Task (N) ─────── (N) Contacts (via Assignees)
Task (N) ─────── (N) Labels (via TaskLabels)

Label (1) ─────── (N) LabelLocale
Label (N) ─────── (N) Tasks (via TaskLabels)
Label (1) ─────── (1) Workflow (optional)

Attachment (1) ─────── (N) AttachmentVersions

Contact (Common) (1) ─────── (N) Tasks (as assignee)
Contact (1) ─────── (1) CustomSchedule (optional)
```

## Integration with Other Modules

### Common Module Integration
- Uses Contact records for task assignees
- Provides user information for task creators and commenters
- Tracks individual work schedules via CustomSchedule

### Sapphire Module Integration
- User authentication and authorization
- User identification for task creation and comments
- Tenant-scoped project and task data

## Project Management Workflows

### Project Initiation Workflow
1. **Create Milestone**: Define project phase with dates
2. **Assign Owner**: Associate user as milestone owner
3. **Estimate Duration**: Set project timeline

### Task Planning Workflow
1. **Create Tasks**: Define work items in milestone
2. **Define Hierarchy**: Create parent-child task structure
3. **Set Estimates**: Add time estimates for capacity planning
4. **Apply Labels**: Mark task type/workflow status
5. **Assign Team**: Distribute tasks to team members

### Task Execution Workflow
1. **Start Task**: Begin work on assigned task
2. **Add Comments**: Collaborate and discuss progress
3. **Attach Files**: Add deliverables and documentation
4. **Update Status**: Change workflow status via labels
5. **Complete Task**: Mark task as done

## Usage Examples

### Create Project with Tasks
```php
// Create milestone
$milestone = Milestone::create([
    'user_id' => auth()->id(),
    'title' => 'Phase 1: Planning',
    'starts_at' => now(),
    'ends_at' => now()->addWeeks(2)
]);

// Create tasks for milestone
$task = Task::create([
    'creator_id' => auth()->id(),
    'milestone_id' => $milestone->id,
    'title' => 'Requirements Gathering',
    'estimated_time' => 16  // 16 hours
]);

// Create subtask
Task::create([
    'parent_id' => $task->id,
    'creator_id' => auth()->id(),
    'title' => 'Interview Stakeholders'
]);
```

### Assign and Collaborate
```php
// Assign task to team members
$task->assignees()->attach($contactIds);

// Add comment
Comment::create([
    'task_id' => $task->id,
    'user_id' => auth()->id(),
    'content' => 'Started work on requirements'
]);

// Attach file
$attachment = Attachment::create([
    'task_id' => $task->id,
    'user_id' => auth()->id(),
    'filename' => 'requirements.pdf'
]);
```

### Track Progress
```php
// Get task with all relationships
$task = Task::with([
    'milestone',
    'assignees',
    'comments.user',
    'attachments.versions',
    'labels.translations',
    'children' // subtasks
])->find($taskId);

// Access assigned team members
$teamMembers = $task->assignees;

// Get discussion history
$comments = $task->comments()->orderBy('created_at', 'desc')->get();
```

## Label & Workflow System

### System Labels
Pre-defined labels include:
- **Workflow Labels**: todo, in-progress, done, blocked
- **Type Labels**: bug, feature, documentation, testing
- **Priority Labels**: low, medium, high, critical

### Custom Labels
Organizations can create custom labels for specific needs:
- Project types
- Client names
- Departments
- Custom tags

## Best Practices

### Task Organization
- Use milestones to group related work
- Create meaningful parent-child hierarchies
- Assign clear descriptions for context
- Use labels consistently for filtering

### Team Collaboration
- Add comments for important decisions
- Attach relevant documentation
- Keep file versions for audit trail
- Update task status regularly

### Scheduling
- Set realistic time estimates
- Define custom schedules for accurate planning
- Monitor milestone deadlines
- Track actual vs. estimated time

## Troubleshooting

### Tasks Not Appearing
- Verify task creation with proper milestone_id or standalone
- Check parent_id relationships for correct hierarchy
- Confirm creator_id user exists

### Assignments Not Working
- Verify contact records exist
- Check e_assignees pivot table entries
- Confirm contact_id and task_id are valid

### Comments Missing
- Verify task_id references valid tasks
- Check user_id relationships
- Confirm created_at timestamps

## Performance Optimization

- Load tasks with eager loading for relationships
- Use pagination for large task lists
- Cache milestone queries frequently accessed
- Index task queries by milestone and user

## Development Notes

- **Priority**: 7
- **Alias**: emerald
- **Service Provider**: `Modules\\Emerald\\App\\Providers\\ModuleServiceProvider`
- **Dependencies**:
  - **Common module** (for Contact management)
  - **Sapphire module** (for user authentication)
- **Required Packages**: astrotomic/laravel-translatable

## Future Enhancements

Planned expansions include:

1. **Time Tracking**: Log actual time spent on tasks
2. **Gantt Charts**: Visual project timeline and dependencies
3. **Resource Leveling**: Optimize team workload distribution
4. **Risk Management**: Track and manage project risks
5. **Budget Tracking**: Monitor project costs
6. **Change Management**: Control scope and change requests
7. **Performance Reporting**: Advanced project analytics
8. **Integration**: Connect with calendar and notification systems
9. **Mobile App**: Task management on mobile devices
10. **Agile Support**: Sprints, burndown charts, and velocity tracking
