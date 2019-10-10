# vfms
## Virtual File Management System

For the class "Info Security in Systems Admin," we were presented the requirements to construct a VFMS, providing an intermediate layer between the user and the operating system's access control. Through this layer, we were able to write a custom logic and permissions system.

Though the minimal solution was to write a native app, this defeated the purpose of the VFMS and eroded the principle of isolation. Instead, we created a web app, which would separate the client both logically and physically from the files they wished to access. A basic POSIX-style access control system was implemented, as well as a from-scratch frontend and backend.
