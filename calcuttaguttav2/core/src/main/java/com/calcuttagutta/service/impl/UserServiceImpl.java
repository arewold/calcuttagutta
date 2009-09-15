package com.calcuttagutta.service.impl;

import java.util.List;

import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.stereotype.Service;

import com.calcuttagutta.model.User;
import com.calcuttagutta.repository.UserRepository;
import com.calcuttagutta.service.UserService;

@Service
public class UserServiceImpl implements UserService {

	@Autowired
	private UserRepository userRepository;
	
	public void saveUser(User user) {
		userRepository.saveUser(user);
	}

	public User getUser(String username) {
		return userRepository.getUser(username);
	}

	public List<User> getAllUsers() {
		return userRepository.getAllUsers();
	}

	public void deleteUser(User user) {
		userRepository.deleteUser(user);
	}
}
