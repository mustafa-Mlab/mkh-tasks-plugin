import React, { useState, useEffect } from 'react';
import TaskList from './components/TaskList';
import TaskForm from './components/TaskForm';
import './App.css';

const App = () => {
    const [tasks, setTasks] = useState([]);
    const [currentTask, setCurrentTask] = useState(null);
    const isAdmin = taskPluginData.userRole === 'administrator'; 

    // Fetch tasks from the REST API
    const fetchTasks = async () => {
        const response = await fetch('/wp-json/mkh-tasks/v1/tasks', {
            headers: {
                'X-WP-Nonce': taskPluginData.nonce,
            },
        });
        const data = await response.json();
        setTasks(data);
    };

    const handleTaskSubmit = async (task) => {
        const url = currentTask ? `/wp-json/mkh-tasks/v1/tasks/${currentTask.id}` : '/wp-json/mkh-tasks/v1/tasks';
        const method = currentTask ? 'PUT' : 'POST';

        await fetch(url, {
            method: method,
            headers: {
                'Content-Type': 'application/json',
                'X-WP-Nonce': taskPluginData.nonce,
            },
            body: JSON.stringify(task),
        });

        setCurrentTask(null);
        fetchTasks();
    };

    const handleTaskDelete = async (id) => {
        await fetch(`/wp-json/mkh-tasks/v1/tasks/${id}`, {
            method: 'DELETE',
            headers: {
                'X-WP-Nonce': taskPluginData.nonce,
            },
        });
        fetchTasks();
    };

    const handleTaskEdit = (task) => {
        setCurrentTask(task);
    };

    useEffect(() => {
        fetchTasks();
    }, []);

    return (
        <div>
            <h1>Tasks</h1>
            {isAdmin && (
                <TaskForm onSubmit={handleTaskSubmit} currentTask={currentTask} />
            )}
            <TaskList 
                tasks={tasks} 
                onEdit={isAdmin ? handleTaskEdit : null} 
                onDelete={isAdmin ? handleTaskDelete : null} 
                isAdmin={isAdmin}
            />
        </div>
    );
};

export default App;
