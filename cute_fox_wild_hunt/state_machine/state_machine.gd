extends Node

class_name StateMachine

var subject
var states: Array
var active_states: Array

func _init(subject):
	self.subject = subject

func add(state: BaseState) -> void:
	if not state in states: 
		states.append(state)
		state.state_machine = self

func enter(state: BaseState, context: Dictionary = {}) -> void:	
	if not state in active_states and state.can_enter(subject):
		state.enter(subject, context)
		active_states.append(state)
			
func leave(state: BaseState) -> void:
	if state in active_states and state.can_leave(subject):
		state.leave(subject)
		active_states.erase(state)
		
func process() -> void:
	for state in active_states: 
		if state.leave_triggered(subject): leave(state)

	for state in states: 
		if state.enter_triggered(subject): enter(state)
	
	for state in active_states: state.process(subject)

	#print('-----------------------------')
	#for state in active_states:
	#	print(state.get_state_name())
	
func get_state(state_name: String) -> BaseState:
	var result: BaseState
	
	for state in states:
		if state.get_state_name() == state_name:
			result = state
			
			break
			
	return result

func is_active(state_name: String) -> bool:
	return get_state(state_name) in active_states
	
func get_active_states_by_type(type: int) -> Array:
	var result: Array = []
	
	for state in active_states:
		if state.get_type() == type:
			result.append(state)
			
	return result
	
func get_states_by_type(type: int) -> Array:
	var result: Array = []
	
	for state in states:
		if state.get_type() == type:
			result.append(state)
			
	return result
	
func clear_but_types(types: Array) -> void:
	for state in states:
		if not state.get_type() in types:
			states.erase(state)
			active_states.erase(state)
	
func clear() -> void:
	states = []
	active_states = []
