package com.calcuttagutta.repository;

import java.util.List;

import com.calcuttagutta.model.User;

public interface UserRepository {

	public abstract void saveUser(User user);

	public abstract User getUser(String username);

	public abstract List<User> getAllUsers();

	public abstract void deleteUser(User user);

}