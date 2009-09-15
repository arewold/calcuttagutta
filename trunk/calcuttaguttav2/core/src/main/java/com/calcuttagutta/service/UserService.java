package com.calcuttagutta.service;

import java.util.List;

import com.calcuttagutta.model.User;

public interface UserService {

	public abstract void saveUser(User user);

	public abstract User getUser(String username);

	public abstract List<User> getAllUsers();

	public abstract void deleteUser(User user);

}