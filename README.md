# MKH Tasks Plugin

**Version**: 1.0  
**Author**: Mustafa Kamal Hossain  
**Description**: A simple task management plugin for WordPress, developed with React.js for the frontend and REST API integration for handling CRUD operations on tasks. This plugin allows administrators to create, update, and delete tasks while allowing other users to view the task list.

## Features
- **WordPress Admin Menu**: Adds a "Tasks" menu in the WordPress dashboard to manage tasks.
- **Task Properties**: Each task includes:
  - **Title**: The name of the task.
  - **Description**: A description providing more details about the task.
  - **Duration**: The estimated time for task completion.
  - **Status**: Task progress status (Pending, In Progress, or Completed).
- **REST API Endpoints**: Allows for creating, reading, updating, and deleting tasks.
- **Frontend React Interface**: Displays tasks on the frontend and includes a form for administrators to add or edit tasks.
- **Shortcode**: `[mkh_task_plugin]` can be used on any page or post to display the task management interface.

## Installation
1. Download the `mkh-tasks-plugin.zip` file.
2. Go to your WordPress dashboard and navigate to **Plugins > Add New**.
3. Click **Upload Plugin** and select the `mkh-tasks-plugin.zip` file.
4. Click **Install Now** and then **Activate** the plugin.

## Usage
### Admin Dashboard
1. Once activated, a "Tasks" menu will appear in the WordPress admin sidebar.
2. Navigate to the "Tasks" menu to add, view, update, or delete tasks.

### Frontend
To display the task management interface on the frontend:
1. Add the `[mkh_task_plugin]` shortcode to any page or post.
2. The shortcode will render a task list and a form for managing tasks, only accessible to administrators.

### REST API Endpoints
The plugin provides the following REST API endpoints:
- **Create Task**: `POST /wp-json/mkh-tasks/v1/tasks`
- **Retrieve Tasks**: `GET /wp-json/mkh-tasks/v1/tasks`
- **Update Task**: `PUT /wp-json/mkh-tasks/v1/tasks/{id}`
- **Delete Task**: `DELETE /wp-json/mkh-tasks/v1/tasks/{id}`

These endpoints allow for easy integration with the frontend React application and are protected by permissions to ensure only administrators can create, update, or delete tasks.

## Development Details
This plugin utilizes:
- **React.js** for building dynamic frontend components.
- **WordPress REST API** for backend operations.
- **React Router** for navigation within the task management interface.
- CSS (or your chosen framework) for styling the frontend.

### File Structure
- `assets/`: Contains the compiled JavaScript files for the React frontend.
- `includes/`: Contains the main PHP class `class-mkh-tasks.php` which handles task management operations.
- `mkh-tasks-plugin.php`: The main plugin file for initializing the plugin and registering REST API routes.

## Contributing
Contributions are welcome! Please feel free to open issues or submit pull requests.

## License
This plugin is open-source and available under the [GPL-2.0 License](https://www.gnu.org/licenses/gpl-2.0.html).
