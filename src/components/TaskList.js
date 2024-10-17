import React from 'react';

const TaskList = ({ tasks, onEdit, onDelete, isAdmin }) => {
    return (
        <div className='task-list'>
            <h3>Task List</h3>
            <table>
                <thead>
                    <tr>
                        <th>Title</th>
                        <th>Description</th>
                        <th>Duration</th>
                        <th>Status</th>
                        {isAdmin && <th>Actions</th>}
                    </tr>
                </thead>
                <tbody>
                    {tasks.map(task => (
                        <tr key={task.id}>
                            <td>{task.title}</td>
                            <td>{task.description}</td>
                            <td>{task.duration}</td>
                            <td>{task.status}</td>
                            {isAdmin && (
                                <td>
                                    <button onClick={() => onEdit(task)}>Edit</button>
                                    <button onClick={() => onDelete(task.id)}>Delete</button>
                                </td>
                            )}
                        </tr>
                    ))}
                </tbody>
            </table>
        </div>
    );
};

export default TaskList;
