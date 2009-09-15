package com.calcuttagutta.repository.jpa;

import java.util.List;

import javax.persistence.EntityManager;
import javax.persistence.PersistenceContext;

import org.springframework.stereotype.Repository;
import org.springframework.transaction.annotation.Propagation;
import org.springframework.transaction.annotation.Transactional;

import com.calcuttagutta.model.User;
import com.calcuttagutta.repository.UserRepository;

@Transactional(propagation=Propagation.REQUIRED, readOnly=true)
@Repository
public class JpaUserRepository implements UserRepository {

	@PersistenceContext
	private EntityManager entityManager;
	
	/*
	 * Methods
	 */
	@Transactional(readOnly=false)
	public void saveUser(User user) {
		entityManager.merge(user);
	}

	public User getUser(String username) {
		return entityManager.find(User.class, username);
	}

	@SuppressWarnings("unchecked")
	public List<User> getAllUsers() {
		return entityManager.createQuery("select u from User u").getResultList();
	}

	@Transactional(readOnly=false)
	public void deleteUser(User user) {
		entityManager.remove(user);
	}
}
