import React, { useState, useEffect } from 'react';

const TaskForm = ({ onSubmit, currentTask }) => {
    const [title, setTitle] = useState('');
    const [description, setDescription] = useState('');
    const [status, setStatus] = useState('');
    const [duration, setDuration] = useState('');

    useEffect(() => {
        if (currentTask) {
            setTitle(currentTask.title);
            setDescription(currentTask.description);
            setStatus(currentTask.status);
            setDuration(currentTask.duration || '');
        } else {
            setTitle('');
            setDescription('');
            setStatus('');
            setDuration('');
        }
    }, [currentTask]);

    const handleSubmit = (e) => {
        e.preventDefault();
        onSubmit({ title, description, status, duration });
    };

    return (
        <form onSubmit={handleSubmit} className='task-form'>
            <h2>{currentTask ? 'Edit Task' : 'Add Task'}</h2>
            <div>
                <label>Title:</label>
                <input 
                    type="text" 
                    value={title} 
                    onChange={(e) => setTitle(e.target.value)} 
                    required 
                />
            </div>
            <div>
                <label>Description:</label>
                <textarea 
                    value={description} 
                    onChange={(e) => setDescription(e.target.value)} 
                    required 
                />
            </div>
            <div>
                <label>Status:</label>
                <select value={status} onChange={(e) => setStatus(e.target.value)} required>
                    <option value="">Select status</option>
                    <option value="pending">Pending</option>
                    <option value="in_progress">In Progress</option>
                    <option value="completed">Completed</option>
                </select>
            </div>
            <div>
                <label>Duration:</label>
                <input 
                    type="text" 
                    value={duration} 
                    onChange={(e) => setDuration(e.target.value)} 
                    placeholder="e.g., 120 munites"
                />
            </div>
            <button type="submit">{currentTask ? 'Update Task' : 'Add Task'}</button>
        </form>
    );
};

export default TaskForm;
