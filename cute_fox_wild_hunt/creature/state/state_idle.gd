extends AliveObjectState

class_name CreatureStateIdle

func enter_triggered(subject) -> bool:
	return true
	
func leave_triggered(subject) -> bool:
	return true
	
func can_enter(subject) -> bool:
	var aura_active_states_count = len(state_machine.get_active_states_by_type(TypeEnum.AURA))
	var context_active_states_count = len(state_machine.get_active_states_by_type(TypeEnum.CONTEXT))
	var active_states_count = len(state_machine.active_states)
	
	return active_states_count - aura_active_states_count - context_active_states_count == 0
	
func can_leave(subject) -> bool:
	var aura_active_states_count = len(state_machine.get_active_states_by_type(TypeEnum.AURA))
	var context_active_states_count = len(state_machine.get_active_states_by_type(TypeEnum.CONTEXT))
	var active_states_count = len(state_machine.active_states)
	
	return active_states_count - aura_active_states_count - context_active_states_count > 1
	
func process(subject)-> void:
	pass
	#subject.creature_sprite.priority_queue("idle", 0)

static func get_state_name() -> String:
	return "idle"
